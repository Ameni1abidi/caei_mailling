<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CampaignAttachmentController extends Controller
{
    /**
     * Display a listing of attachments.
     */
    public function index(Request $request)
    {
        $query = CampaignAttachment::with('campaign')->latest();

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('type')) {
            $query->where('attachment_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhereHas('campaign', function ($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%");
                  });
            });
        }

        $attachments = $query->paginate(15)->withQueryString();
        $campaigns = Campaign::orderBy('nom')->get();
        $types = CampaignAttachment::types();

        $stats = [
            'total' => CampaignAttachment::count(),
            'total_size' => CampaignAttachment::sum('file_size'),
            'pdf_count' => CampaignAttachment::where('attachment_type', CampaignAttachment::TYPE_PROGRAMME_PDF)->count(),
            'brochure_count' => CampaignAttachment::where('attachment_type', CampaignAttachment::TYPE_BROCHURE)->count(),
        ];

        return view('attachments.index', compact('attachments', 'campaigns', 'types', 'stats'));
    }

    /**
     * Show the form for creating a new attachment.
     */
    public function create()
    {
        $campaigns = Campaign::orderBy('nom')->get();
        $types = CampaignAttachment::types();

        return view('attachments.create', compact('campaigns', 'types'));
    }

    /**
     * Store newly created attachment(s) in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'campaign_id' => 'required|exists:campaigns,id',
            'attachment_type' => [
                'nullable',
                'string',
                Rule::in(array_keys(CampaignAttachment::types())),
            ],
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt|max:15360',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt|max:15360',
        ];

        $request->validate($rules, [
            'campaign_id.required' => 'La campagne est obligatoire.',
            'campaign_id.exists' => 'La campagne sélectionnée est invalide.',
            'file.mimes' => 'Le fichier doit être au format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR, TXT.',
            'file.max' => 'Le fichier ne doit pas dépasser 15 Mo.',
            'files.*.mimes' => 'Chaque fichier doit être au format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR, TXT.',
            'files.*.max' => 'Chaque fichier ne doit pas dépasser 15 Mo.',
        ]);

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return back()->withErrors(['file' => 'Veuillez sélectionner au moins un fichier à téléverser.'])->withInput();
        }

        $campaignId = $request->input('campaign_id');
        $attachmentType = $request->input('attachment_type');
        $count = 0;

        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) continue;

            $path = $file->store('attachments', 'public');

            // If an attachment type is specified and unique constraint applies, replace existing if found
            if ($attachmentType) {
                $existing = CampaignAttachment::where('campaign_id', $campaignId)
                    ->where('attachment_type', $attachmentType)
                    ->first();

                if ($existing) {
                    if (Storage::disk('public')->exists($existing->file_path)) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
                    $existing->update([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                    $count++;
                    continue;
                }
            }

            CampaignAttachment::create([
                'campaign_id' => $campaignId,
                'attachment_type' => $attachmentType,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
            ]);
            $count++;
        }

        $message = $count > 1
            ? "{$count} pièces jointes ajoutées avec succès."
            : "Pièce jointe ajoutée avec succès.";

        if ($request->has('redirect_to_campaign')) {
            return redirect()->route('campaigns.edit', $campaignId)->with('success', $message);
        }

        return redirect()->route('attachments.index')->with('success', $message);
    }

    /**
     * Download the specified attachment file.
     */
    public function download(CampaignAttachment $attachment)
    {
        if (!$attachment->file_path || !Storage::disk('public')->exists($attachment->file_path)) {
            return back()->with('error', 'Fichier introuvable sur le serveur.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Remove the specified attachment from storage.
     */
    public function destroy(CampaignAttachment $attachment)
    {
        $campaignId = $attachment->campaign_id;

        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        if (request()->has('redirect_to_campaign')) {
            return redirect()->route('campaigns.edit', $campaignId)->with('success', 'Pièce jointe supprimée.');
        }

        return redirect()->route('attachments.index')->with('success', 'Pièce jointe supprimée.');
    }
}

@extends('layouts.app')

@section('title', 'PDF Preview — '.$invoice->invoice_number)

@section('content')
<div class="mb-6">
    <a href="{{ route('invoices.show', $invoice) }}" class="text-sm text-slate-500 hover:text-slate-700">← Back to Invoice</a>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mt-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">PDF Preview &amp; HTML Editor</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $invoice->invoice_number }} — edit HTML below, then refresh preview or download PDF</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="refreshPreview()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50">
                Refresh Preview
            </button>
            <button type="button" onclick="resetHtml()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50">
                Reset HTML
            </button>
            <button type="button" onclick="openInNewTab()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50">
                Open in New Tab
            </button>
            <button type="button" onclick="downloadPdf()" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-violet-700">
                Download PDF
            </button>
        </div>
    </div>
</div>

<div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl px-4 py-3 mb-4">
    <strong>Tip:</strong> Edit the HTML in the editor or use browser DevTools (F12 → Console) on the preview iframe.
    Changes here are temporary — update <code class="text-xs bg-amber-100 px-1 rounded">resources/views/invoices/pdf.blade.php</code> to make them permanent.
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4" style="height: calc(100vh - 280px); min-height: 500px;">
    {{-- Live preview --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">Live Preview</span>
            <span class="text-xs text-slate-400">A4 portrait</span>
        </div>
        <div class="flex-1 bg-slate-100 p-4 overflow-auto">
            <iframe id="preview-frame" title="PDF Preview" class="w-full bg-white shadow-lg mx-auto" style="min-height: 800px; max-width: 794px; border: 1px solid #e2e8f0;"></iframe>
        </div>
    </div>

    {{-- HTML editor --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">HTML Source</span>
            <span id="line-count" class="text-xs text-slate-400"></span>
        </div>
        <textarea id="html-editor" spellcheck="false" class="flex-1 w-full p-4 font-mono text-xs leading-relaxed text-slate-800 bg-slate-900 text-green-300 resize-none outline-none border-0" style="tab-size: 2;"></textarea>
    </div>
</div>

<form id="pdf-download-form" action="{{ route('invoices.pdf', $invoice) }}" method="POST" target="_blank" class="hidden">
    @csrf
    <input type="hidden" name="html" id="pdf-html-input">
</form>
@endsection

@push('scripts')
<script>
const originalHtml = @json($html);
const editor = document.getElementById('html-editor');
const frame = document.getElementById('preview-frame');
const lineCount = document.getElementById('line-count');

editor.value = originalHtml;
updateLineCount();

editor.addEventListener('input', updateLineCount);

function updateLineCount() {
    lineCount.textContent = editor.value.split('\n').length + ' lines';
}

function refreshPreview() {
    frame.srcdoc = editor.value;
}

function resetHtml() {
    if (!confirm('Reset HTML to the original template?')) return;
    editor.value = originalHtml;
    updateLineCount();
    refreshPreview();
}

function openInNewTab() {
    const win = window.open('', '_blank');
    win.document.write(editor.value);
    win.document.close();
}

function downloadPdf() {
    document.getElementById('pdf-html-input').value = editor.value;
    document.getElementById('pdf-download-form').submit();
}

// Initial preview load
refreshPreview();

// Ctrl+S / Cmd+S to refresh preview
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        refreshPreview();
    }
});
</script>
@endpush

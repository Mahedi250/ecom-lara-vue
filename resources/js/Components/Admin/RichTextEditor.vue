<template>
    <div class="border border-gray-200 rounded-xl overflow-hidden">
        <div v-if="editor" class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 px-2 py-1.5">
            <button type="button" @click="editor.chain().focus().toggleBold().run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('bold') }" title="Bold">
                <strong>B</strong>
            </button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()"
                class="toolbar-btn italic" :class="{ 'toolbar-btn-active': editor.isActive('italic') }" title="Italic">
                I
            </button>
            <button type="button" @click="editor.chain().focus().toggleUnderline().run()"
                class="toolbar-btn underline" :class="{ 'toolbar-btn-active': editor.isActive('underline') }" title="Underline">
                U
            </button>
            <button type="button" @click="editor.chain().focus().toggleStrike().run()"
                class="toolbar-btn line-through" :class="{ 'toolbar-btn-active': editor.isActive('strike') }" title="Strikethrough">
                S
            </button>
            <span class="w-px h-5 bg-gray-200 mx-1"></span>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('heading', { level: 2 }) }" title="Heading">
                H2
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('heading', { level: 3 }) }" title="Subheading">
                H3
            </button>
            <span class="w-px h-5 bg-gray-200 mx-1"></span>
            <button type="button" @click="editor.chain().focus().setTextAlign('left').run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive({ textAlign: 'left' }) }" title="Align left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h13"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('center').run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive({ textAlign: 'center' }) }" title="Align center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('right').run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive({ textAlign: 'right' }) }" title="Align right">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M7 18h13"/></svg>
            </button>
            <span class="w-px h-5 bg-gray-200 mx-1"></span>
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('bulletList') }" title="Bullet list">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('orderedList') }" title="Numbered list">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M4 6h1v4M4 10h2M4 14h1.5a1.5 1.5 0 010 3H4"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()"
                class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('blockquote') }" title="Quote">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h4v4H8c0 2-1 3-3 3m9-7h4v4h-3c0 2-1 3-3 3"/></svg>
            </button>
            <span class="w-px h-5 bg-gray-200 mx-1"></span>
            <button type="button" @click="setLink" class="toolbar-btn" :class="{ 'toolbar-btn-active': editor.isActive('link') }" title="Insert link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().unsetLink().run()" v-if="editor.isActive('link')" class="toolbar-btn" title="Remove link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <span class="w-px h-5 bg-gray-200 mx-1"></span>
            <button type="button" @click="editor.chain().focus().undo().run()" class="toolbar-btn" title="Undo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L4 10l5-5M4 10h10a5 5 0 015 5v1"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().redo().run()" class="toolbar-btn" title="Redo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l5-5-5-5M20 10H10a5 5 0 00-5 5v1"/></svg>
            </button>
            <button type="button" @click="editor.chain().focus().unsetAllMarks().clearNodes().run()" class="toolbar-btn text-[11px]" title="Clear formatting">
                Clear
            </button>
        </div>
        <EditorContent :editor="editor" class="prose-editor px-4 py-3 max-h-[420px] overflow-y-auto" />
    </div>
</template>

<script setup>
import { onBeforeUnmount, watch } from 'vue';
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write page content...' },
});
const emit = defineEmits(['update:modelValue']);

const editor = new Editor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Placeholder.configure({ placeholder: props.placeholder }),
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Underline,
        Link.configure({ openOnClick: false, autolink: true }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

function setLink() {
    const previousUrl = editor.getAttributes('link').href;
    const url = window.prompt('URL', previousUrl || 'https://');
    if (url === null) return;
    if (url === '') {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
}

watch(() => props.modelValue, (value) => {
    if (value !== editor.getHTML()) {
        editor.commands.setContent(value, false);
    }
});

onBeforeUnmount(() => {
    editor.destroy();
});
</script>

<style scoped>
.toolbar-btn { @apply w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-200 transition; }
.toolbar-btn-active { @apply bg-indigo-100 text-indigo-700; }
</style>

<style>
.prose-editor .ProseMirror { outline: none; font-size: 0.875rem; color: #374151; min-height: 200px; }
.prose-editor .ProseMirror p { margin-bottom: 0.75rem; }
.prose-editor .ProseMirror h2 { font-size: 1.15rem; font-weight: 700; color: #111827; margin: 1rem 0 0.5rem; }
.prose-editor .ProseMirror h3 { font-size: 1.05rem; font-weight: 700; color: #111827; margin: 0.875rem 0 0.5rem; }
.prose-editor .ProseMirror ul { list-style: disc; margin: 0 0 0.75rem 1.25rem; }
.prose-editor .ProseMirror ol { list-style: decimal; margin: 0 0 0.75rem 1.25rem; }
.prose-editor .ProseMirror li { margin-bottom: 0.25rem; }
.prose-editor .ProseMirror blockquote { border-left: 3px solid #d1d5db; padding-left: 0.75rem; color: #6b7280; margin: 0.75rem 0; }
.prose-editor .ProseMirror a { color: #4f46e5; text-decoration: underline; }
.prose-editor .ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    float: left;
    height: 0;
    pointer-events: none;
}
</style>

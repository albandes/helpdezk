(function ($) {
    /**
     * Plugin Summernote: Pagebreak com marcador visual e HTML limpo
     */
    $.summernote.plugins.pagebreak = function (context) {
        const ui = $.summernote.ui;
        const $editable = context.layoutInfo.editable;

        const hasFA =
            document.querySelector('link[href*="fontawesome"]') ||
            document.querySelector('script[src*="fontawesome"]');
        const icon = hasFA ? '<i class="fas fa-grip-lines"></i>' : '🗎';

        // === UTILITÁRIOS ===

        function createMarker() {
            const marker = document.createElement('div');
            marker.className = 'pagebreak-marker';
            marker.setAttribute('contenteditable', 'false');
            marker.setAttribute('data-pagebreak', 'true');
            marker.innerHTML = hasFA
                ? '<i class="fas fa-grip-lines"></i> Quebra de página — clique para remover.'
                : '🗎 Quebra de página — clique para remover.';
            return marker;
        }

        function createHr() {
            const hr = document.createElement('hr');
            hr.className = 'note-pagebreak';
            hr.setAttribute('contenteditable', 'false');
            hr.setAttribute('data-pagebreak', '1');
            return hr;
        }

        function sanitizeHTML(html) {
            if (!html) return html;
            let sanitized = html
                .replace(/<p>\s*<\/p>/gi, '')
                .replace(/<\/p>\s*<\/p>/gi, '</p>')
                .replace(/<p>\s*<p>/gi, '<p>')
                .replace(/(<br\s*\/?>){3,}/gi, '<br><br>');
            return sanitized;
        }

        function getCurrentParagraph(range) {
            if (!range || !range.commonAncestorContainer) return null;
            let node = range.commonAncestorContainer;
            if (node.nodeType === 3) node = node.parentNode;
            while (node && node.nodeType === 1) {
                if (node.tagName.toLowerCase() === 'p') return node;
                node = node.parentNode;
            }
            return null;
        }

        // === INSERÇÃO DE PAGEBREAK ===

        function insertPagebreak() {
            let range = context.invoke('editor.getLastRange');
            if (!range) {
                context.invoke('editor.focus');
                range = context.invoke('editor.createRange');
            }

            const marker = createMarker();
            const hr = createHr();
            const fragment = document.createDocumentFragment();
            fragment.appendChild(marker);
            fragment.appendChild(hr);

            const currentParagraph = getCurrentParagraph(range);

            if (currentParagraph && currentParagraph.parentNode) {
                currentParagraph.parentNode.insertBefore(fragment, currentParagraph.nextSibling);
            } else {
                range.insertNode(fragment);
            }

            createEditableParagraphAfter(hr);
            context.invoke('editor.focus');
            context.invoke('editor.afterCommand');
        }

        function createEditableParagraphAfter(pagebreakElement) {
            let next = pagebreakElement.nextElementSibling;
            while (next && (next.nodeType === 3 || next.nodeType === 8) && !next.textContent.trim()) {
                next = next.nextElementSibling;
            }

            if (
                !next ||
                $(next).hasClass('pagebreak-marker') ||
                $(next).hasClass('note-pagebreak') ||
                (next.tagName && next.tagName.toLowerCase() === 'br')
            ) {
                const newP = document.createElement('p');
                newP.innerHTML = '<br>';
                pagebreakElement.parentNode.insertBefore(newP, pagebreakElement.nextSibling);

                const range = document.createRange();
                range.setStart(newP, 0);
                range.collapse(true);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }

        // === LIMPEZA E NORMALIZAÇÃO ===

        function normalizePagebreaks(html) {
            if (!html || typeof html !== 'string') return html;
            let cleaned = html;

            cleaned = cleaned.replace(/<p>\s*<\/p>/gi, '');
            cleaned = cleaned.replace(/<\/p>\s*<\/p>/gi, '</p>');
            cleaned = cleaned.replace(/<p>\s*<p>/gi, '<p>');

            cleaned = cleaned.replace(
                /<p>(.*?)<pagebreak\s*\/?>(.*?)<\/p>/gis,
                (match, before, after) => {
                    let result = '';
                    if (before && before.trim()) result += `<p>${before}</p>`;
                    result += '<pagebreak />';
                    if (after && after.trim()) result += `<p>${after}</p>`;
                    return result;
                }
            );

            cleaned = cleaned.replace(/<pagebreak><\/pagebreak>/gi, '<pagebreak />');
            cleaned = cleaned.replace(/(<pagebreak\s*\/?>\s*){2,}/gi, '<pagebreak />');
            cleaned = cleaned.replace(/<\/p>\s*<\/p>/gi, '</p>');
            cleaned = cleaned.replace(/<p>\s*<p>/gi, '<p>');
            cleaned = cleaned.replace(/\s*(<\/?pagebreak\s*\/?>)\s*/gi, '$1');

            return cleaned.trim();
        }

        function getCleanContent(html) {
            if (!html) return html;
            const sanitizedHtml = sanitizeHTML(html);
            const $temp = $('<div>').html(sanitizedHtml);

            $temp.find('.pagebreak-marker').remove();
            $temp.find('hr.note-pagebreak').replaceWith('<pagebreak />');

            let cleanHtml = $temp.html();
            cleanHtml = normalizePagebreaks(cleanHtml);
            return cleanHtml;
        }

        // === INTERAÇÃO ===

        function removePagebreak($marker) {
            const $hr = $marker.next('hr.note-pagebreak');
            $marker.remove();
            if ($hr.length) $hr.remove();
            context.invoke('editor.focus');
        }

        function replaceSavedPagebreaks() {
            const currentHtml = $editable.html();
            const sanitizedHtml = sanitizeHTML(currentHtml);
            if (sanitizedHtml !== currentHtml) $editable.html(sanitizedHtml);

            $editable.find('pagebreak').each(function () {
                const $pb = $(this);
                const marker = createMarker();
                const hr = createHr();
                $pb.before(marker);
                marker.after(hr);
                $pb.remove();
            });
        }

        // === BOTÃO TOOLBAR ===

        context.memo('button.pagebreak', function () {
            return ui.button({
                contents: icon,
                tooltip: 'Inserir quebra de página',
                click: insertPagebreak
            }).render();
        });

        context.memo('editor.getCleanContent', function () {
            return getCleanContent($editable.html());
        });

        // === INICIALIZAÇÃO ===

        const originalOnInit = context.options.callbacks.onInit;
        context.options.callbacks.onInit = function () {
            setTimeout(() => replaceSavedPagebreaks(), 100);

            $editable.on('click', '.pagebreak-marker', function (e) {
                e.preventDefault();
                e.stopPropagation();
                removePagebreak($(this));
            });

            if (typeof originalOnInit === 'function') originalOnInit.apply(this, arguments);
        };

        const originalDestroy = context.options.callbacks.onDestroy;
        context.options.callbacks.onDestroy = function () {
            $editable.off('click', '.pagebreak-marker');
            if (typeof originalDestroy === 'function') originalDestroy.apply(this, arguments);
        };
    };

    // === ESTILOS ===
    const style = document.createElement('style');
    style.textContent = `
    .pagebreak-marker {
        display: block;
        text-align: center;
        border-top: 2px dashed #999;
        border-bottom: 2px dashed #999;
        color: #555;
        background: #f8f8f8;
        padding: 8px 12px;
        margin: 20px 0;
        border-radius: 4px;
        font-size: 0.9em;
        cursor: pointer;
        user-select: none;
    }
    .pagebreak-marker:hover { 
        background:#eaeaea; 
        color:#222; 
    }
    .pagebreak-marker i { 
        margin-right:6px; 
        color:#777; 
    }
    hr.note-pagebreak { 
        display: none !important;
    }
    `;
    document.head.appendChild(style);

    // === HELPER GLOBAL ===
    if (!$.summernote.getCleanContent) {
        $.summernote.getCleanContent = function ($editor) {
            if (!$editor || !$editor.length || !$editor.data('summernote')) return '';
            return $editor.summernote('invoke', 'getCleanContent');
        };
    }
})(jQuery);

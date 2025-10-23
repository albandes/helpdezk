(function ($) {
    /**
     * Plugin Summernote: Inserir <pagebreak /> com marcador visual e espaço extra
     */
    $.summernote.plugins.pagebreak = function (context) {
        const ui = $.summernote.ui;
        const $editable = context.layoutInfo.editable;

        // Detecta FontAwesome
        const hasFA =
            document.querySelector('link[href*="fontawesome"]') ||
            document.querySelector('script[src*="fontawesome"]');

        const icon = hasFA
            ? '<i class="fas fa-grip-lines"></i>'
            : '<i class="note-icon-minus"></i>';

        /**
         * Cria marcador visual
         */
        function createMarker() {
            const marker = document.createElement('div');
            marker.classList.add('pagebreak-marker');
            marker.setAttribute('contenteditable', 'false');
            marker.innerHTML = hasFA
                ? '<i class="fas fa-grip-lines"></i> Quebra de página (não será exibida no PDF) — clique sobre ela para remover.'
                : '🗎 Quebra de página (não será exibida no PDF) — clique sobre ela para remover.';
            return marker;
        }

        /**
         * Insere a quebra de página na posição do cursor e adiciona espaço extra
         */
        function insertPagebreak() {
            const range = context.invoke('editor.createRange');
            if (!range) return;

            const marker = createMarker();
            const pagebreak = document.createElement('pagebreak');
            const br = document.createElement('p'); // espaço extra após a quebra
            br.innerHTML = '<br>'; // garante linha em branco

            // Insere no cursor
            range.insertNode(pagebreak);
            $(pagebreak).before(marker);
            $(pagebreak).after(br);

            // Move o cursor após a linha extra
            const sel = window.getSelection();
            const newRange = document.createRange();
            newRange.setStartAfter(br);
            newRange.collapse(true);
            sel.removeAllRanges();
            sel.addRange(newRange);

            // Foca o editor
            context.invoke('editor.focus');
        }

        /**
         * Remove marcador e tag correspondente
         */
        function removePagebreak($marker) {
            const $pagebreak = $marker.next('pagebreak');
            const $br = $pagebreak.next('p'); // remove o parágrafo extra também
            $marker.remove();
            $pagebreak.remove();
            $br.remove();
        }

        /**
         * Substitui <pagebreak /> por marcadores visuais
         */
        function replacePagebreaks() {
            $editable.find('pagebreak').each(function () {
                const $el = $(this);
                if ($el.prev('.pagebreak-marker').length === 0) {
                    const marker = createMarker();
                    $el.before(marker);
                }
            });
        }

        /**
         * Restaura <pagebreak /> reais e remove marcadores
         */
        function restorePagebreaks() {
            $editable.find('.pagebreak-marker').each(function () {
                const $marker = $(this);
                if ($marker.next('pagebreak').length === 0) {
                    $marker.after('<pagebreak />');
                }
                $marker.remove();
            });
        }

        /**
         * Botão da toolbar — apenas insere a quebra
         */
        context.memo('button.pagebreak', function () {
            return ui.button({
                contents: icon,
                tooltip: 'Inserir quebra de página',
                click: insertPagebreak
            }).render();
        });

        /**
         * Callbacks e eventos
         */
        const originalOnInit = context.options.callbacks.onInit;
        context.options.callbacks.onInit = function () {
            replacePagebreaks();

            // Clique no marcador para remover
            $editable.on('click', '.pagebreak-marker', function (e) {
                e.preventDefault();
                removePagebreak($(this));
            });

            if (typeof originalOnInit === 'function') {
                originalOnInit.apply(this, arguments);
            }
        };

        const originalOnBlur = context.options.callbacks.onBlur;
        context.options.callbacks.onBlur = function () {
            restorePagebreaks();
            if (typeof originalOnBlur === 'function') {
                originalOnBlur.apply(this, arguments);
            }
        };
    };

    /**
     * Estilos visuais do marcador
     */
    const style = document.createElement('style');
    style.textContent = `
        .pagebreak-marker {
            display: block;
            text-align: center;
            border-top: 2px dashed #999;
            border-bottom: 2px dashed #999;
            color: #555;
            background: #f8f8f8;
            padding: 4px 8px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 0.9em;
            cursor: pointer;
            user-select: none;
        }
        .pagebreak-marker:hover {
            background: #eaeaea;
            color: #222;
        }
        .pagebreak-marker i {
            margin-right: 6px;
            color: #777;
        }
    `;
    document.head.appendChild(style);
})(jQuery);

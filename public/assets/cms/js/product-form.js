(function () {
    function initImageDropzones() {
        document.querySelectorAll('[data-image-dropzone]').forEach(function (zone) {
            var uploadRoot = zone.closest('[data-image-upload]');
            var inputId = zone.getAttribute('data-file-input') || 'sf-field-image-file';
            var input = document.getElementById(inputId);
            var pathInput = uploadRoot ? uploadRoot.querySelector('[name="image"]') : document.getElementById('sf-field-image');
            var preview = zone.querySelector('[data-image-preview]');
            var hint = zone.querySelector('[data-image-dropzone-text]');
            var removeBtn = zone.querySelector('[data-image-remove]');
            var removeFlag = uploadRoot ? uploadRoot.querySelector('[data-image-remove-flag]') : null;

            if (!input) {
                return;
            }

            function setHasImage(hasImage) {
                zone.classList.toggle('sf-product-image-dropzone--has-image', hasImage);
                if (removeBtn) {
                    removeBtn.hidden = !hasImage;
                }
                if (preview) {
                    preview.hidden = !hasImage;
                    if (!hasImage) {
                        preview.removeAttribute('src');
                    }
                }
                if (hint) {
                    hint.hidden = hasImage;
                }
            }

            function showPreview(file) {
                if (!file || !file.type.startsWith('image/')) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function () {
                    if (preview) {
                        preview.src = reader.result;
                    }
                    setHasImage(true);
                    if (removeFlag) {
                        removeFlag.value = '0';
                    }
                };
                reader.readAsDataURL(file);
            }

            function clearImage(event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                input.value = '';
                if (pathInput) {
                    pathInput.value = '';
                }
                if (removeFlag) {
                    removeFlag.value = '1';
                }
                setHasImage(false);
            }

            function preventDefaults(event) {
                event.preventDefault();
                event.stopPropagation();
            }

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
                zone.addEventListener(eventName, preventDefaults, false);
            });

            zone.addEventListener('dragenter', function () {
                zone.classList.add('is-dragover');
            });

            zone.addEventListener('dragover', function () {
                zone.classList.add('is-dragover');
            });

            zone.addEventListener('dragleave', function () {
                zone.classList.remove('is-dragover');
            });

            zone.addEventListener('drop', function (event) {
                zone.classList.remove('is-dragover');
                var files = event.dataTransfer && event.dataTransfer.files;
                if (!files || !files.length) {
                    return;
                }

                var file = files[0];
                if (!file.type.startsWith('image/')) {
                    return;
                }

                var transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
                showPreview(file);
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            zone.addEventListener('click', function (event) {
                if (event.target.closest('[data-image-remove]')) {
                    return;
                }
                if (event.target === input) {
                    return;
                }
                input.click();
            });

            if (removeBtn) {
                removeBtn.addEventListener('click', clearImage);
            }

            input.addEventListener('change', function () {
                if (input.files && input.files[0]) {
                    showPreview(input.files[0]);
                } else if (!pathInput || !pathInput.value.trim()) {
                    clearImage();
                }
            });
        });
    }

    function initMultiselects() {
        document.querySelectorAll('[data-product-multiselect]').forEach(function (root) {
            var fieldName = root.getAttribute('data-field-name');
            var products = JSON.parse(root.getAttribute('data-products') || '[]');
            var selected = JSON.parse(root.getAttribute('data-selected') || '[]').map(Number);
            var field = root.querySelector('[data-ms-field]');
            var input = root.querySelector('[data-ms-input]');
            var tagsEl = root.querySelector('[data-ms-tags]');
            var menu = root.querySelector('[data-ms-menu]');
            var hidden = root.querySelector('[data-ms-hidden]');

            if (!field || !input || !menu || !hidden) {
                return;
            }

            function syncHidden() {
                hidden.innerHTML = '';
                selected.forEach(function (id) {
                    var node = document.createElement('input');
                    node.type = 'hidden';
                    node.name = fieldName + '[]';
                    node.value = String(id);
                    hidden.appendChild(node);
                });
            }

            function renderTags() {
                tagsEl.innerHTML = '';
                selected.forEach(function (id) {
                    var product = products.find(function (item) { return item.id === id; });
                    if (!product) {
                        return;
                    }

                    var tag = document.createElement('span');
                    tag.className = 'sf-ms-tag';
                    var tagText = document.createElement('span');
                    tagText.textContent = product.name;
                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.setAttribute('aria-label', 'Remove');
                    removeBtn.textContent = '×';
                    removeBtn.addEventListener('click', function (event) {
                        event.stopPropagation();
                        selected = selected.filter(function (value) { return value !== id; });
                        renderTags();
                        syncHidden();
                        renderMenu(input.value);
                    });
                    tag.appendChild(tagText);
                    tag.appendChild(removeBtn);
                    tagsEl.appendChild(tag);
                });
            }

            function renderMenu(query) {
                var q = (query || '').trim().toLowerCase();
                menu.innerHTML = '';

                var available = products.filter(function (product) {
                    if (selected.indexOf(product.id) !== -1) {
                        return false;
                    }
                    if (q === '') {
                        return true;
                    }
                    return product.name.toLowerCase().indexOf(q) !== -1;
                });

                if (available.length === 0) {
                    var empty = document.createElement('div');
                    empty.className = 'sf-ms-empty';
                    empty.textContent = q === '' ? 'All products selected' : 'No matches';
                    menu.appendChild(empty);
                    return;
                }

                available.slice(0, 40).forEach(function (product) {
                    var option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'sf-ms-option';
                    option.setAttribute('role', 'option');
                    var strong = document.createElement('strong');
                    strong.textContent = product.name;
                    var span = document.createElement('span');
                    span.textContent = '#' + product.id;
                    option.appendChild(strong);
                    option.appendChild(span);
                    option.addEventListener('click', function (event) {
                        event.preventDefault();
                        selected.push(product.id);
                        renderTags();
                        syncHidden();
                        input.value = '';
                        renderMenu('');
                        input.focus();
                    });
                    menu.appendChild(option);
                });
            }

            function openMenu() {
                menu.hidden = false;
                field.setAttribute('aria-expanded', 'true');
                renderMenu(input.value);
            }

            function closeMenu() {
                menu.hidden = true;
                field.setAttribute('aria-expanded', 'false');
            }

            field.addEventListener('click', function () {
                input.focus();
                openMenu();
            });

            input.addEventListener('focus', openMenu);

            input.addEventListener('input', function () {
                openMenu();
                renderMenu(input.value);
            });

            document.addEventListener('click', function (event) {
                if (!root.contains(event.target)) {
                    closeMenu();
                }
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                    input.blur();
                }
            });

            renderTags();
            syncHidden();
        });
    }

    function initGalleryManager() {
        var manager = document.querySelector('[data-gallery-manager]');
        if (!manager) {
            return;
        }

        var list = manager.querySelector('[data-gallery-list]');
        var template = manager.querySelector('[data-gallery-template]');
        var nextIndexInput = manager.querySelector('[data-gallery-next-index]');
        var removeInputs = manager.querySelector('[data-gallery-remove-inputs]');
        var nextIndex = parseInt(nextIndexInput.value, 10) || 0;

        manager.querySelector('[data-gallery-add]').addEventListener('click', function () {
            var clone = template.content.cloneNode(true);
            var row = clone.querySelector('[data-gallery-row]');
            var idx = nextIndex++;
            nextIndexInput.value = String(nextIndex);

            row.querySelector('[data-gallery-file]').name = 'gallery_new[' + idx + '][image_file]';
            row.querySelector('[data-gallery-path]').name = 'gallery_new[' + idx + '][image]';
            row.querySelector('[data-gallery-alt]').name = 'gallery_new[' + idx + '][alt_text]';
            row.querySelector('[data-gallery-sort]').name = 'gallery_new[' + idx + '][sort_order]';

            list.appendChild(row);
        });

        manager.addEventListener('click', function (event) {
            var removeNew = event.target.closest('[data-gallery-remove-new]');
            if (removeNew) {
                removeNew.closest('[data-gallery-row]').remove();
                return;
            }

            var removeExisting = event.target.closest('[data-gallery-remove-existing]');
            if (removeExisting) {
                var id = removeExisting.getAttribute('data-gallery-remove-existing');
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'gallery_remove[]';
                hidden.value = id;
                removeInputs.appendChild(hidden);
                removeExisting.closest('[data-gallery-row]').remove();
            }
        });
    }

    function initFeaturedToggle() {
        var checkbox = document.getElementById('sf-field-featured');
        var label = document.querySelector('.sf-product-visibility__switch-text');
        if (!checkbox || !label) {
            return;
        }

        checkbox.addEventListener('change', function () {
            label.textContent = checkbox.checked ? 'On' : 'Off';
        });
    }

    initImageDropzones();
    initMultiselects();
    initGalleryManager();
    initFeaturedToggle();
})();

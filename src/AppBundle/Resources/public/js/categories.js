(function ($) {
    var $nameField = $("#appbundle_category_name");

    if (!$nameField.length) return;

    var CreateCategory = function () {
        this.$iconField = $("#pictogram");
        this.$container = $("#icons-container");
        this.$modal = $("#createModal");
        this.$nameField = this.$modal.find("#appbundle_category_name");
        this.$placeHolder = this.$modal.find(".placeholder");

        this.init();
    };

    CreateCategory.prototype = {
        LOOKUP_DELAY: 1000,
        $nameField: null,
        $iconField: null,
        $container: null,
        $modal: null,
        $placeHolder: null,
        isIconIndependent: false,
        lookupTimeout: 0,
        init: function () {
            this.eventListeners();
        },
        eventListeners: function () {
            var self = this;

            this.$nameField.on('keyup', function () {
                if (self.isIconIndependent) return;

                self.$iconField.val($(this).val()).trigger('keyup');
            });

            this.$iconField.on('keyup', function () {

                if (self.lookupTimeout) {
                    clearTimeout(self.lookupTimeout);
                }

                self.lookupTimeout = window.setTimeout(function () {
                    if (self.$iconField.val().length < 3) {
                        return;
                    }

                    self.lookUp(self.$iconField.val());
                }, self.LOOKUP_DELAY);
            });

            this.$container.on("click", ".icon", function (e) {
                e.preventDefault();

                self.selectIcon($(this).attr('data-icon-id'));
            });

            this.$modal.on('click', '.save-changes', function (e) {
                e.preventDefault();

                self.submitForm();

                return false;
            });
        },
        lookUp: function (searchString) {
            var self = this;
            self.preload(true);

            $.ajax({
                url: FRONT.baseUrl + '/icon/searchByName/' + encodeURIComponent(searchString),
                method: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    self.renderIcons(data);
                },
                error: function (x, y, z) {

                },
                complete: function () {
                    self.preload(false);
                }
            });
        },
        preload: function (load) {
            load = load || false;

            this.$container.toggleClass('loading', load);
        },
        renderIcons: function (icons) {
            var $list = this.$container.find(".icon-list"), ln = icons.length, i;

            $list.html("");

            if (!ln) {
                $list.html('<p style="margin-top: 45px;" class="text-center"><em>Geen pictogrammen gevonden.</em></p>');

                return;
            }

            for (i = 0; i < ln; i++) {
                var icon = icons[i];

                var $icon = this.renderIcon(icon);

                $list.append($icon);
            }
        },
        renderIcon: function (icon) {
            return $('<div class="col-xs-6 col-sm-4 col-md-3">' +
            '<a href="javascript:void(null);" title="' + icon.name + '" data-icon-id="' + icon.id + '" class="icon">' +
            '<img src="/bundles/app/images/icons/' + icon.icon + '" class="img-responsive">' +
            '<h2>' + icon.name + '</h2>' +
            '</a>' +
            '</div>');
        },
        selectIcon: function (iconId) {
            var $icon = this.getIconById(iconId);

            this.$container.find(".icon.selected").removeClass('selected');

            $icon.addClass('selected');
        },
        getIconById: function (iconId) {
            var $icon = this.$container.find(".icon[data-icon-id='" + iconId + "']");

            return $icon.length ? $icon : false;
        },
        submitForm: function () {
            var $selectedIcon = this.$container.find(".icon.selected"),
                $form = this.$modal.find("form"),
                parent = $form.find("#parent").val(),
                self = this;

            if ($selectedIcon.length) {
                $form.find("#appbundle_category_icon").val($selectedIcon.attr("data-icon-id"));
            }

            if (parent != '') {
                $form.find("#appbundle_category_parent").val(parent);
            }

            $.ajax({
                url: FRONT.baseUrl + '/addCategory',
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        location.reload();
                    } else if (data.title && data.message) {
                        self.$placeHolder.alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$placeHolder.alertWindow("Fout", "Kon categorie niet toevoegen.");
                },
                complete: function () {
                }
            });
        }
    };

    var EditCategory = function () {
        this.$modal = $("#editModal");

        this.init();
    };

    EditCategory.prototype = {
        LOOKUP_DELAY: 1000,
        $nameField: null,
        $iconField: null,
        $container: null,
        $modal: null,
        $placeHolder: null,
        isIconIndependent: false,
        lookupTimeout: 0,
        categoryId: 0,
        init: function () {
            this.eventListeners();
        },
        initModal: function () {
            this.$iconField = this.$modal.find("#pictogram");
            this.$container = this.$modal.find("#icons-container");
            this.$nameField = this.$modal.find("#appbundle_category_name");
            this.$placeHolder = this.$modal.find(".placeholder");
        },
        eventListeners: function () {
            var self = this;

            $(document).on('click', '.editBtn', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var categoryId = $(this).closest('.category').attr('data-category-id');

                if (!categoryId) return;

                self.loadCategory(categoryId);
            });
        },
        modalEventListeners: function () {
            var self = this;

            this.$nameField.off('keyup');
            this.$nameField.on('keyup', function () {
                if (self.isIconIndependent) return;

                self.$iconField.val($(this).val()).trigger('keyup');
            });

            this.$iconField.off('keyup');
            this.$iconField.on('keyup', function () {

                if (self.lookupTimeout) {
                    clearTimeout(self.lookupTimeout);
                }

                self.lookupTimeout = window.setTimeout(function () {
                    if (self.$iconField.val().length < 3) {
                        return;
                    }

                    self.lookUp(self.$iconField.val());
                }, self.LOOKUP_DELAY);
            });

            this.$container.off("click", ".icon");
            this.$container.on("click", ".icon", function (e) {
                e.preventDefault();

                self.selectIcon($(this).attr('data-icon-id'));
            });

            this.$modal.off('click', '.save-changes');
            this.$modal.on('click', '.save-changes', function (e) {
                e.preventDefault();

                self.submitForm();

                return false;
            });

            this.$modal.off('click', '.delete');
            this.$modal.on('click', '.delete', function (e) {
                e.preventDefault();

                self.deleteCategory();

                return false;
            });
        },
        loadCategory: function (categoryId) {
            var self = this;

            this.categoryId = categoryId;

            this.$modal.find(".modal-body").load(FRONT.baseUrl + '/editForm/' + encodeURIComponent(categoryId),
                function () {
                    self.$modal.modal('show');
                    self.initModal();
                    self.modalEventListeners();
                });
        },
        lookUp: function (searchString) {
            var self = this;
            self.preload(true);

            $.ajax({
                url: FRONT.baseUrl + '/icon/searchByName/' + encodeURIComponent(searchString),
                method: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    self.renderIcons(data);
                },
                error: function (x, y, z) {

                },
                complete: function () {
                    self.preload(false);
                }
            });
        },
        preload: function (load) {
            load = load || false;

            this.$container.toggleClass('loading', load);
        },
        renderIcons: function (icons) {
            var $list = this.$container.find(".icon-list"), ln = icons.length, i;

            $list.html("");

            if (!ln) {
                $list.html('<p style="margin-top: 45px;" class="text-center"><em>Geen pictogrammen gevonden.</em></p>');

                return;
            }

            for (i = 0; i < ln; i++) {
                var icon = icons[i];

                var $icon = this.renderIcon(icon);

                $list.append($icon);
            }
        },
        renderIcon: function (icon) {
            return $('<div class="col-xs-6 col-sm-4 col-md-3">' +
            '<a href="javascript:void(null);" title="' + icon.name + '" data-icon-id="' + icon.id + '" class="icon">' +
            '<img src="/bundles/app/images/icons/' + icon.icon + '" class="img-responsive">' +
            '<h2>' + icon.name + '</h2>' +
            '</a>' +
            '</div>');
        },
        selectIcon: function (iconId) {
            var $icon = this.getIconById(iconId);

            this.$container.find(".icon.selected").removeClass('selected');

            $icon.addClass('selected');
        },
        getIconById: function (iconId) {
            var $icon = this.$container.find(".icon[data-icon-id='" + iconId + "']");

            return $icon.length ? $icon : false;
        },
        submitForm: function () {
            var $selectedIcon = this.$container.find(".icon.selected"),
                $form = this.$modal.find("form"),
                self = this;

            if ($selectedIcon.length) {
                $form.find("#appbundle_category_icon").val($selectedIcon.attr("data-icon-id"));
            }

            $.ajax({
                url: FRONT.baseUrl + '/editCategory/' + encodeURIComponent(this.categoryId),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        location.reload();
                    } else if (data.title && data.message) {
                        self.$placeHolder.alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$placeHolder.alertWindow("Fout", "Kon categorie niet bewerken.");
                }
            });
        },
        deleteCategory: function () {
            if (this.categoryId <= 0) return;

            if (!confirm("Ben je zeker dat je deze categorie wil verwijderen?")) return;

            var self = this;

            $.ajax({
                url: FRONT.baseUrl + '/deleteCategory/' + encodeURIComponent(this.categoryId),
                method: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        location.reload();
                    } else if (data.title && data.message) {
                        self.$placeHolder.alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$placeHolder.alertWindow("Fout", "Kon categorie niet verwijderen.");
                }
            });
        }
    };

    window.createCategory = new CreateCategory();
    window.editCategory = new EditCategory();

})(jQuery);
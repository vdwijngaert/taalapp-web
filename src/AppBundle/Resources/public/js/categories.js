(function ($) {
    var $nameField = $("#appbundle_category_name");

    if (!$nameField.length) return;

    var IconFinder = function () {
        this.$nameField = $nameField;
        this.$iconField = $("#pictogram");
        this.$container = $("#icons-container");
        this.$modal = $("#createModal");
        this.$placeHolder = this.$modal.find(".placeholder");

        this.init();
    };

    IconFinder.prototype = {
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

            this.$modal.on('click', '.save-changes', function(e) {
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
        submitForm: function() {
            var $selectedIcon = this.$container.find(".icon.selected"),
                $form = this.$modal.find("form"),
                parent = $form.find("#parent").val(),
                self = this;

            if($selectedIcon.length) {
                $form.find("#appbundle_category_icon").val($selectedIcon.attr("data-icon-id"));
            }

            if(parent != '') {
                $form.find("#appbundle_category_parent").val(parent);
            }

            $.ajax({
                url: FRONT.baseUrl + '/addCategory',
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if(data === true) {
                        location.reload();
                    } else if(data.title && data.message) {
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

    window.iconFinder = new IconFinder();

})(jQuery);
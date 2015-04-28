(function($) {
    var $container = $("#questions");

    if (!$container.length) return;

    var Question = function () {
        this.init();
    };

    Question.prototype = {
        $container: null,
        $list: null,
        $createModal: null,
        $editModal: null,
        questionId: 0,
        init: function () {
            this.$container = $("#questions");
            this.$createModal = $("#createQuestionModal");
            this.$editModal = $("#editQuestionModal");
            this.$list = this.$container.find(".question-list");

            this.eventListeners();
        },
        eventListeners: function () {
            var self = this;

            this.$createModal.on('click', '.save-changes', function (e) {
                e.preventDefault();

                self.addQuestion();
            });

            this.$list.on('click', 'li a', function (e) {
                e.preventDefault();

                var questionId = $(this).attr('data-question-id');

                if (!questionId) return;

                self.editQuestion(parseInt(questionId));
            });

            this.$editModal.on('click', '.save-changes', function (e) {
                e.preventDefault();

                self.submitEditedQuestion();

                return false;
            });

            this.$editModal.on('click', '.delete', function (e) {
                e.preventDefault();

                self.deleteQuestion();

                return false;
            });
        },
        addQuestion: function () {
            var $form = this.$createModal.find("form");

            $.ajax({
                url: FRONT.baseUrl + '/question/add/' + encodeURIComponent(currentCategoryId),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        location.reload();
                    } else if (data.title && data.message) {
                        console.log(self.$createModal, self.$createModal.find(".placeholder"), data);
                        self.$createModal.find(".placeholder").alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$createModal.find(".placeholder").alertWindow("Fout", "Kon zin niet toevoegen.");
                },
                complete: function () {
                }
            });
        },
        submitEditedQuestion: function () {
            var $form = this.$editModal.find("form"), self = this;

            $.ajax({
                url: FRONT.baseUrl + '/question/edit/' + self.questionId,
                method: 'POST',
                data: $form.serialize(),
                dataType: 'JSON',
                success: function (data) {
                    if (data === true) {
                        location.reload();
                    } else if (data.title && data.message) {
                        console.log(self.$createModal, self.$createModal.find(".placeholder"), data);
                        self.$createModal.find(".placeholder").alertWindow(data.title, data.message, "danger", data.errors);
                    }
                },
                error: function (x, y, z) {
                    self.$createModal.find(".placeholder").alertWindow("Fout", "Kon zin niet bewerken.");
                },
                complete: function () {
                }
            });
        },
        editQuestion: function (questionId) {
            var self = this;
            this.questionId = questionId;

            this.$editModal.find(".modal-body").load(FRONT.baseUrl + '/question/editForm/' + encodeURIComponent(questionId),
                function () {
                    self.$editModal.modal('show');
                }
            );
        },
        deleteQuestion: function() {
            if (this.questionId <= 0) return;

            if (!confirm("Ben je zeker dat je deze zin wil verwijderen?")) return;

            var self = this;

            $.ajax({
                url: FRONT.baseUrl + '/question/delete/' + this.questionId,
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
                    self.$placeHolder.alertWindow("Fout", "Kon zin niet verwijderen.");
                }
            });
        }
    };

    window.question = new Question();
})(jQuery);
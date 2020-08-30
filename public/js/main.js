$( document ).ready(function() {
    if (window.location.pathname.indexOf('/t/') != -1) {
        if (Test.isStarted()) {
            Test.updateData(Test.continue);
        }
    } else if (window.location.pathname.indexOf('/new') != -1) {
        setTimeout(function () {
            $('#sub-question').hide();
        }, 300);
    }
});

function confirmDialog(question, callback) {
    var me = this;
    
    $.confirm({
        title: 'Подтверждение',
        content: question,
        buttons: {
            confirm: {
                text: 'Да',
                btnClass: 'btn btn-success',
                action: function() {
                    callback.call(me);
                }
            }, 
            cancel: {
                text: 'Нет',
                btnClass: 'btn btn-light'
            }
        }
    });
}

function errorDialog(msg) {
    $.confirm({
        title: 'Ошибка!',
        content: msg,
        type: 'red',
        typeAnimated: true,
        buttons: {
            close: {
                text: 'Закрыть',
                btnClass: 'btn btn-light'
            }
        }
    });
}

function autoHideAlert(msg, timer){
    timer = timer || 300;

    $.alert({
        title: 'Системное сообщение',
        content: msg,
        autoClose: 'ok|' + timer,
    });
    
}

function setTestLength(length) {
    $("#test-length-value").text(length);
}

function getSlug() {
    return slug = $("#slug").val();
}

function simpleAjax(params) {
    var me = this;
    
    if (typeof params.method == 'undefined') {
        params.method = 'POST';
    }
    if (typeof params.dataType == 'undefined') {
        params.dataType = 'json';
    }
    
    $.ajax({
        url: params.url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: params.method,
        dataType: params.dataType,
        data: params.data,
        success: function(data) { 
            params.success.call(me, data);
        }
    });
}

Wizard = (function () {
    var curStep = 0,
        data = {
            questions: '',
            test_length: 0,
            description: '',
            email: ''
        },
        step1Validator = function (callback) {
            var res = {
                isValid: true,
                msg: ''
            };

            if (data.questions.length == 0) {
                res.isValid = false;
                res.msg = 'Необходимо добавить хоть один вопрос';
            } else {
                $.each(data.questions, function (index, question) {
                    if (question.questionText == '') {
                        res.isValid = false;
                        res.msg = 'Необходимо указать текст вопроса';

                        return;
                    } else {
                        var isCorrectAnswerSet = false;

                        $.each(question.answers, function (i, answer) {
                            if (answer.answerText == '') {
                                res.isValid = false;
                                res.msg = 'Необходимо указать текст ответа';

                                return;
                            } else if (answer.isTrue) {
                                isCorrectAnswerSet = true;
                            }
                        });

                        if (!res.isValid) {
                            return;
                        } else if (!isCorrectAnswerSet) {
                            res.isValid = false;
                            res.msg = 'Необходимо указать правильный ответ';

                            return;
                        }
                    }
                });
            }

            callback.call(this, res);
        };

    return {
        back: function () {
            curStep--;
            Wizard.show();
        },
        next: function () {
            if (curStep == 0) {
                data.questions = QuestionEdit.prepareDataForSaving();

                step1Validator(function (response) {
                    if (response.isValid) {
                        curStep++;
                        Wizard.show();
                    } else {
                        errorDialog(response.msg);
                    }
                });

                return;
            } else if (curStep == 1) {
                data.test_length = $('#test-length').val();
                data.description = $('#test-description').val();

                if (data.description == '') {
                    return errorDialog('Необходимо указать описание теста');
                }
            } else if (curStep == 2) {
                data.email = $('#user-email').val();

                if (!validateEmail(data.email)) {
                    errorDialog('Ваш email пустой или не валидный');
                    return;
                }

                TestEdit.createTest(data, function (result) {
                    $('#test-slug').val(result.testSlug);
                    $('#email-address').text(result.email);

                    curStep++;
                    Wizard.show();
                });

                return;
            }

            curStep++;
            Wizard.show();
        },
        show: function () {
            for (var i = 0; i < 4; i++) {
                if (i == curStep) {
                    $('.step' + (i + 1) + '-container').show();
                } else {
                    $('.step' + (i + 1) + '-container').hide();
                }
            }
        }
    }
})();

QuestionEdit = (function() {
    var getAnswers = function (answerContainer) {
        var answers = $(answerContainer).find(".answers-container"),
            params = {};

        if (answers.length > 0) {
            answers = answers[0];

            var me = this,
                params = {
                    questionText: $(answerContainer).find('textarea').val(),
                    answers: []
                };

            $(answers).children().each(function (index, answer) {
                var answerItem = {
                        isTrue: false,
                        answerText: $(answer).find('input[type="text"]').val()
                    };

                if ($(answer).find('input[type="checkbox"]')[0].checked) {
                    answerItem.isTrue = true;
                }

                params.answers.push(answerItem);
            });
        }

        return params;
    };

    return {
        prepareDataForSaving: function () {
            var result = [];

            $('.question-edit-container').each(function (i, row) {
                result.push(getAnswers(row));
            });

            return result;
        },
        add: function (btn) {
            $(btn).attr('disabled', true);

            simpleAjax({
                url: '/get_question_form',
                data: {
                    slug: getSlug()
                },
                success: function(data) {
                    $(btn).attr('disabled', false);

                    if (data.success) {
                        $('.questions-container').append(data.html);
                        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        },
        del: function (btn) {
            $(btn).parent().parent().remove();
        }
    };
})();

AnswerEdit = (function() {
    return {
        del: function (btn) {
            $(btn).parent('.edit-answer-container').remove();
        },
        add: function (btn) {
            $(btn).addClass('disabled-container');

            simpleAjax({
                url: '/get_answer_form',
                success: function(data) {
                    $(btn).removeClass('disabled-container');

                    if (data.success) {
                        $(btn).parent().parent().children('.answers-container').append(data.html);
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        }
        // ,
        // addNew: function(btn, questionId) {
        //     $(btn).addClass('disabled-container');
        //
        //     simpleAjax({
        //         url: '/get_answer_form',
        //         data: {
        //             slug: getSlug(),
        //             questionId: questionId
        //         },
        //         success: function(data) {
        //             $(btn).removeClass('disabled-container');
        //
        //             if (data.success) {
        //                 $("#answers-container-" + questionId).append(data.html);
        //             } else {
        //                 errorDialog(data.message);
        //             }
        //         }
        //     });
        // },
        // delete: function(btn, questionId, answerId) {
        //     var me = this;
        //
        //     confirmDialog('Удалить ответ?', function() {
        //         $(btn).addClass('disabled-container');
        //
        //         simpleAjax({
        //             url: '/delete_answer',
        //             data: {
        //                 slug: getSlug(),
        //                 questionId: questionId,
        //                 answerId: answerId
        //             },
        //             success: function(data) {
        //                 $(btn).removeClass('disabled-container');
        //
        //                 if (data.success) {
        //                     $("#answer-edit-container-" + questionId + "-" + answerId).remove();
        //                 } else {
        //                     errorDialog(data.message);
        //                 }
        //             }
        //         });
        //     });
        // }
    };
})();

TestEdit = (function () {
    var copyText = function (str) {
        const el = document.createElement('textarea');
        el.value = str;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    };

    return {
        copyTestLink: function () {
            copyText($('#test-slug').val());
        },
        onTestLinkClick: function () {
            window.open($('#test-slug').val());
        },
        activate: function () {
            confirmDialog('Вы действительно хотите активировать тест?<br/><br/>Убедитесь что вы сохранили все данные', function () {
                simpleAjax({
                    url: '/change_status',
                    data: {
                        slug: getSlug(),
                        is_active: 1
                    },
                    success: function(data) {
                        if (data.success) {
                            autoHideAlert('Тест активирован', 3000);
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000);
                        } else {
                            errorDialog(data.message);
                        }
                    }
                });
            });
        },
        deactivate: function () {
            confirmDialog('Вы действительно хотите деактивировать тест?', function () {
                simpleAjax({
                    url: '/change_status',
                    data: {
                        slug: getSlug(),
                        is_active: 0
                    },
                    success: function(data) {
                        if (data.success) {
                            autoHideAlert('Тест деактивирован', 3000);
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000);
                        } else {
                            errorDialog(data.message);
                        }
                    }
                });
            });
        },
        onLinkClick: function (url) {
            window.open(url);
        },
        copyEditLink: function (url) {
            copyText(url);
        },
        copyTestingLink: function (url) {
            copyText(url);
        },
        createTest: function (data, callback) {
            simpleAjax({
                url: '/save_new',
                data: {
                    params: JSON.stringify(data),
                    sub_question: $('#sub-question').val()
                },
                success: function(res) {
                    if (res.success) {
                        callback(res);
                    } else {
                        errorDialog(res.message);
                    }
                }
            });
        },
        save: function (e, callback) {
            var form = $('.edit-test-form'),
                questions = QuestionEdit.prepareDataForSaving();

            simpleAjax({
                url: '/save_test',
                data: {
                    slug: getSlug(),
                    questions: JSON.stringify(questions),
                    form: JSON.stringify(form.serializeArray())
                },
                success: function(data) {
                    if (data.success) {
                        autoHideAlert('Данные сохранены');

                        if (callback) {
                            callback.call(this, data);
                        }
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        }
    }
})();

Test = (function() {
    var me = this, timer = 0;

    me.validateEmail = function($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( $email );
    };

    me.renderTimer = function(seconds) {
        var date = new Date(0);
        var timeString = '';

        date.setSeconds(seconds);

        if (seconds >= 60 * 60) {
            timeString = date.toISOString().substr(11, 8);
        } else {
            timeString = date.toISOString().substr(14, 5);
        }

        $("#test-timer").text('Время на прохождение: ' + timeString + ' минут');
    };

    me.startTest = function(resultId, email, testLengthInSeconds) {
        localStorage.setItem('timer', testLengthInSeconds);
        localStorage.setItem('resultId', resultId);
        localStorage.setItem('email', email);

        timer = setInterval(function() {
            if (testLengthInSeconds <= 0) {
                clearInterval(timer);
                localStorage.removeItem('resultId');
                localStorage.removeItem('timer');
                localStorage.removeItem('email');

                Test.finish($("#finish-test"));
                return;
            }

            testLengthInSeconds--;

            localStorage.setItem('timer', testLengthInSeconds);

            me.renderTimer(testLengthInSeconds);
        }, 1000);
    };

    return {
        isStarted: function() {
            return (localStorage.getItem('timer') != null && localStorage.getItem('resultId') != null);
        },
        updateData: function (callback) {
            var slug = $('#test-preview-container').data('slug');

            simpleAjax({
                url: '/get_info',
                data: {
                    slug: slug,
                    result_id: localStorage.getItem('resultId')
                },
                success: function(data) {
                    if (data.success) {
                        localStorage.setItem('timer', data.seconds_to_end);

                        callback.call(this, data);
                    } else {
                        localStorage.removeItem('timer');
                        localStorage.removeItem('resultId');
                        localStorage.removeItem('email');
                    }
                }
            });
        },
        continue: function() {
            $("#test-timer").text('Вычисляется оставшееся время ...');

            me.startTest(
                localStorage.getItem('resultId'),
                localStorage.getItem('email'),
                localStorage.getItem('timer')
            );

            $("#test-process-container").data('resultid', localStorage.getItem('resultId'));
            $("#test-process-container").show();
            $("#tested-email").val(localStorage.getItem('email'));
            $("#tested-email").attr('disabled', true);
            $("#start-testing").attr('disabled', true);
        },
        start: function(btn) {
            var email = $('#tested-email').val(),
                container = $('#test-preview-container');

            if (!validateEmail(email)) {
                errorDialog('Ваш email пустой или не валидный');
                return;
            }

            container.addClass('disabled-container');

            simpleAjax({
                url: '/start_test',
                data: {
                    slug: container.data('slug'),
                    email: email
                },
                success: function(data) {
                    container.removeClass('disabled-container');

                    if (data.success) {
                        $("#test-process-container").data('resultid', data.result_id);
                        $("#test-process-container").show();

                        $("#tested-email").attr('disabled', true);
                        $(btn).attr('disabled', true);

                        startTest(data.result_id, email, data.time);
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        },
        finish: function (btn) {
            var result = [],
                container = $("#test-process-container"),
                testedEmail = $("#tested-email").val(),
                resultId = $("#test-process-container").data('resultid');

            if (testedEmail == '' || !validateEmail(testedEmail)) {
                errorDialog('Необходимо ввести ваш email');
                return;
            }

            $(container).addClass('disabled-container');

            $(".t-answer-item").each(function (i, answerRow) {
                result.push({
                    question_id: $(answerRow).data('questionid'),
                    answer_id: $(answerRow).data('answerid'),
                    checked: $(answerRow).data('checked')
                });
            });

            simpleAjax({
                url: '/finish_test',
                data: {
                    slug: container.data('slug'),
                    email: testedEmail,
                    result_id: resultId,
                    data: JSON.stringify(result)
                },
                success: function(data) {
                    $(container).removeClass('disabled-container');

                    if (data.success) {
                        $(btn).remove();
                        autoHideAlert('Тест завершен');

                        clearInterval(timer);
                        localStorage.removeItem('timer');
                        localStorage.removeItem('resultId');
                        localStorage.removeItem('email');
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        },
        answerClick: function (container, questionId, answerId) {
            if (!$(container).data('checked')) {
                $(container).data('checked', true);
                $(container).children('i').css('color', 'blue');
            } else {
                $(container).data('checked', false);
                $(container).children('i').css('color', 'gainsboro');
            }
        }
    };
})();

Results = (function () {
    return {
        show: function () {
            window.location.href = "/r/" + getSlug();
        }
    }
})();
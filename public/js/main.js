$( document ).ready(function() {
    console.log( "ready!" );

    if (window.location.pathname.indexOf('/t/') != -1) {
        // if (Test.isStarted()) {
        //     Test.continue();
        // }

        // Test.start();

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

function autoHideAlert(msg){
    $.alert({
        title: 'Системное сообщение',
        content: msg,
        autoClose: 'ok|300',    
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

QuestionEdit = (function() {
    var getAnswers = function (questionId) {
        var answers = $("#answers-container-" + questionId),
            params = {};

        if (answers.length > 0) {
            answers = answers[0];

            var me = this,
                params = {
                    questionId: questionId,
                    questionText: $('#edit-question-' + questionId).val(),
                    answers: []
                };
                // container = $("#question-edit-container-" + questionId);
// debugger;
//             container.addClass('disabled-container');

            $(answers).children().each(function (index, answer) {
                var answerId = $(answer).attr('id'),
                    ids = answerId.split('answer-edit-container-')[1],
                    idsSplitted = ids.split('-'),
                    answerItem = {
                        isTrue: false,
                        answerId: idsSplitted[1],
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
        delete: function(btn, questionId) {
            var me = this;
            
            confirmDialog('Удалить вопрос?', function() {
                var container = $("#question-edit-container-" + questionId);
                
                container.addClass('disabled-container');
            
                simpleAjax({
                    url: '/delete_question',
                    data: {
                        slug: getSlug(),
                        questionId: questionId
                    },
                    success: function(data) {
                        container.removeClass('disabled-container');

                        if (data.success) {
                            container.remove();
                        } else {
                            errorDialog(data.message);
                        }
                    }
                });
            });
        },
        prepareDataForSaving: function () {
            var result = [];

            $('.question-edit-container').each(function (i, row) {
                var questionId = $(row).attr('id').split('question-edit-container-')[1];

                result.push(getAnswers(questionId));
            });

            return result;
        },
        addNew: function(btn) {
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
        }
    };
})();

AnswerEdit = (function() {
    return {
        delete: function(btn, questionId, answerId) {
            var me = this;
            
            confirmDialog('Удалить ответ?', function() {
                $(btn).addClass('disabled-container');
            
                simpleAjax({
                    url: '/delete_answer',
                    data: {
                        slug: getSlug(),
                        questionId: questionId,
                        answerId: answerId
                    },
                    success: function(data) {
                        $(btn).removeClass('disabled-container');

                        if (data.success) {
                            $("#answer-edit-container-" + questionId + "-" + answerId).remove();
                        } else {
                            errorDialog(data.message);
                        }
                    }
                });
            });
        },
        addNew: function(btn, questionId) {
            $(btn).addClass('disabled-container');
            
            simpleAjax({
                url: '/get_answer_form',
                data: {
                    slug: getSlug(),
                    questionId: questionId
                },
                success: function(data) { 
                    $(btn).removeClass('disabled-container');
                    
                    if (data.success) {
                        $("#answers-container-" + questionId).append(data.html);
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
        }
    };
})();

TestEdit = (function () {
    return {
        save: function (e) {
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
                    debugger;
                    // container.removeClass('disabled-container');
                    //
                    // if (data.success) {
                    //     container.remove();
                    // } else {
                    //     errorDialog(data.message);
                    // }
                }
            });
        }
    }
})();

Test = (function() {
    var me = this;

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

        $("#test-timer").text(timeString);
    };

    me.startTest = function(testLengthInSeconds) {
        localStorage.setItem('timer', testLengthInSeconds);

        var timer = setInterval(function() {
            if (testLengthInSeconds <= 0) {
                clearInterval(timer);

                debugger;
//TODO: отправить результат на сервер автоматом и убрать спрятать вопросы

                return;
            }

            testLengthInSeconds--;

            localStorage.setItem('timer', testLengthInSeconds);

            me.renderTimer(testLengthInSeconds);
        }, 1000);
    };

    return {
        isStarted: function() {
            return (localStorage.getItem('timer') != null);
        }, 
        isFinished: function() {
            if (this.isStarted()) {
                if (parseInt(localStorage.getItem('timer') <= 0)) {
                    debugger;
                }
            }

            return false;
        },
        continue: function() {
            if (localStorage.getItem('timer') == null) {
                return;
            }

            me.startTest(localStorage.getItem('timer'));

            $("#test-preview-container").hide();
            $("#test-process-container").show();
        },
        start: function(btn) {
            var email = $('#tested-email').val(),
                container = $('#test-preview-container');

            if (!validateEmail(email)) {
                errorDialog('Ваш email пустой или не валидный');
                return;
            }

            container.addClass('disabled-container');

            // localStorage.clear();
            // localStorage.setItem('name', name);

            // me.startTest(testLength * 60);

            // $("#test-preview-container").hide();
            // $("#test-process-container").show();

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
                        // $(container).remove();
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
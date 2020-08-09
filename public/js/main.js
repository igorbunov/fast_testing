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

$( document ).ready(function() {
    console.log( "ready!" );
//     $('[data-toggle="tooltip"]').tooltip();
//localStorage.clear();
    if (window.location.pathname.indexOf('/t/') != -1) {
        if (Test.isStarted()) {
            Test.continue();
        }
    }
});

QuestionEdit = (function() {
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
        save: function(btn, questionId) {
            var answers = $("#answers-container-" + questionId);

            if (answers.length > 0) {
                answers = answers[0];

                var me = this,
                    params = {
                        questionId: questionId,
                        questionText: $('#edit-question-' + questionId).val(),
                        answers: []
                    },
                    container = $("#question-edit-container-" + questionId);
// debugger;
                container.addClass('disabled-container');

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

            simpleAjax({
                url: '/save_question',
                data: {
                    slug: getSlug(),
                    params: params
                },
                success: function(data) {
                    container.removeClass('disabled-container');

                    if (data.success) {
                        $.alert('Данные сохранены');
                    } else {
                        errorDialog(data.message);
                    }
                }
            });
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
            var form = $('.edit-test-form');

            simpleAjax({
                url: '/save_test',
                data: {
                    slug: getSlug(),
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
        start: function(testLength) {
            var name = $('#tested-name').val();

            localStorage.clear();
            localStorage.setItem('name', name);

            me.startTest(testLength * 60);

            $("#test-preview-container").hide();
            $("#test-process-container").show();
        }
    };
})();
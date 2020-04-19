/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



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
            var me = this,
                container = $("#question-edit-container-" + questionId);
                
            container.addClass('disabled-container');

            simpleAjax({
                url: '/save_question',
                data: {
                    slug: getSlug(),
                    questionId: questionId
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
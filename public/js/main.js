$( document ).ready(function() {
    if (isPage('/t/')) {
        if (Test.isStarted()) {
            Test.updateData(Test.continue);
        }
    } else if (isPage('/new')) {
        setTimeout(function () {
            $('#sub-question').hide();
        }, 300);
    } else if (isPage('/feedback')) {
        Feedback.subscribe();
    }

    $(function(){
        $('.selectpicker').selectpicker('val', document.documentElement.lang);
    });

    $('.selectpicker').on('change', function(e){
        simpleAjax({
            url: '/set_lang',
            data: {
                slug: getSlug(),
                lang: this.value
            },
            success: function() {
                window.location.reload();
            }
        });
    });
});

function isPage(partUrl) {
    return (window.location.pathname.indexOf(partUrl) != -1)
}

function confirmDialog(question, callback) {
    var me = this;
    
    $.confirm({
        title: window.translation['confirmation'],
        content: question,
        buttons: {
            confirm: {
                text: window.translation['yes'],
                btnClass: 'btn btn-success',
                action: function() {
                    callback.call(me);
                }
            }, 
            cancel: {
                text: window.translation['no'],
                btnClass: 'btn btn-light'
            }
        }
    });
}

function errorDialog(msg) {
    $.confirm({
        title: window.translation['error'] + '!',
        content: msg,
        type: 'red',
        typeAnimated: true,
        buttons: {
            close: {
                text: window.translation['close'],
                btnClass: 'btn btn-light'
            }
        }
    });
}

function autoHideAlert(msg, timer){
    timer = timer || 300;

    $.alert({
        title: window.translation['system message'],
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
                res.msg = window.translation['you must add at least one question'];
            } else {
                $.each(data.questions, function (index, question) {
                    if (question.questionText == '') {
                        res.isValid = false;
                        res.msg = window.translation['you must specify the text of the question'];

                        return;
                    } else {
                        var isCorrectAnswerSet = false;

                        $.each(question.answers, function (i, answer) {
                            if (answer.answerText == '') {
                                res.isValid = false;
                                res.msg = window.translation['you must specify the response text'];

                                return;
                            } else if (answer.isTrue) {
                                isCorrectAnswerSet = true;
                            }
                        });

                        if (!res.isValid) {
                            return;
                        } else if (!isCorrectAnswerSet) {
                            res.isValid = false;
                            res.msg = window.translation['you must provide the correct answer'];

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
                    return errorDialog(window.translation['test description required']);
                }
            } else if (curStep == 2) {
                data.email = $('#user-email').val();

                if (!validateEmail(data.email)) {
                    errorDialog(window.translation['your email is empty or not valid']);
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
        deactivate: function () {
            confirmDialog(window.translation['do you really want to deactivate the test'] + '?', function () {
                simpleAjax({
                    url: '/change_status',
                    data: {
                        slug: getSlug(),
                        is_active: 0
                    },
                    success: function(data) {
                        if (data.success) {
                            autoHideAlert(window.translation['test deactivated'], 3000);
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

        $("#test-timer").text(window.translation['time for testing'] + ': ' + timeString + ' ' + window.translation['minutes']);
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
            $("#test-timer").text(window.translation['calculating time for testing'] + ' ...');

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
        onEmailEnter: function (e) {
            if (e.keyCode == 13) {
                Test.start($('#start-testing'));
            }
        },
        start: function(btn) {
            var email = $('#tested-email').val(),
                container = $('#test-preview-container');

            if (!validateEmail(email)) {
                errorDialog(window.translation['your email is empty or not valid']);
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
                errorDialog(window.translation['you must enter your email']);
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
                        autoHideAlert(window.translation['test completed']);

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

Feedback = (function () {
    return {
        subscribe: function () {
            var form = $("#feedback-form");

            form.submit(function (e) {
                e.preventDefault();

                if (!validateEmail(form.find('input[name="email"]').val())) {
                    errorDialog(window.translation['your email is empty or not valid']);
                    return;
                }

                form.addClass('disabled-container');

                simpleAjax({
                    url: '/add_feedback',
                    data: form.serialize(),
                    success: function(data) {
                        form.removeClass('disabled-container');

                        if (data.success) {
                            form.trigger('reset');

                            autoHideAlert(data.message);
                        } else {
                            errorDialog(data.message);
                        }
                    }
                });


            });
        }
    }
})();
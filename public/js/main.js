/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$( document ).ready(function() {
    console.log( "ready!" );
     $('[data-toggle="tooltip"]').tooltip();
     
     $('.delete-question').click(function() {
         $.confirm({
            title: 'Подтверждение',
            content: 'Удалить вопрос?',
            buttons: {
                confirm: {
                    text: 'Да',
                    btnClass: 'btn btn-success',
                    action: function(){
                        $.alert('Удаляем');
                    }
                }, 
                cancel: {
                    text: 'Нет',
                    btnClass: 'btn btn-light'
                }
            }
        });
     });
     
     $('.delete-answer').click(function() {
         $.confirm({
            title: 'Подтверждение',
            content: 'Удалить ответ?',
            buttons: {
                confirm: {
                    text: 'Да',
                    btnClass: 'btn btn-success',
                    action: function(){
                        $.alert('Удаляем');
                    }
                }, 
                cancel: {
                    text: 'Нет',
                    btnClass: 'btn btn-light'
                }
            }
        });
     });
     
     
});

function setTestLength(length) {
    $("#test-length-value").text(length);
}
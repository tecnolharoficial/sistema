//Raiz
var PATH = window.location.protocol + '//' + window.location.host + '/sistema/';
var currentPageBase = window.location.pathname;
//-----------------------------------------------------------------------------------

//Jquery Mask
(function($) {
    function applyMasks() {
        $('.date').mask('00/00/0000');
        $('.time').mask('00:00');
        $('.date_time').mask('00/00/0000 00:00:00');
        $('.cep').mask('00000-000');
        $('.phone').mask('0000-0000');
        $('.phone_with_ddd').mask('(00) 0000-0000');
        $('.phone_us').mask('(000) 000-0000');
        $('.mixed').mask('AAA 000-S0S');
        $('.ip_address').mask('099.099.099.099');
        $('.percent').mask('##0,00%', {reverse: true});
        $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
        $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
        $('.fallback').mask("00r00r0000", {
            translation: {
                'r': {
                    pattern: /[\/]/,
                    fallback: '/'
                },
                placeholder: "__/__/____"
            }
        });
        $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
        $('.cep_with_callback').mask('00000-000', {
            onComplete: function(cep) {
                console.log('Mask is done!:', cep);
            },
            onKeyPress: function(cep, event, currentField, options){
                console.log('A key was pressed!:', cep, ' event: ', event, 'currentField: ', currentField.attr('class'), ' options: ', options);
            },
            onInvalid: function(val, e, field, invalid, options){
                var error = invalid[0];
                console.log ("Digit: ", error.v, " is invalid for the position: ", error.p, ". We expect something like: ", error.e);
            }
        });
        $('.crazy_cep').mask('00000-000', {onKeyPress: function(cep, e, field, options){
            var masks = ['00000-000', '0-00-00-00'];
            mask = (cep.length>7) ? masks[1] : masks[0];
            $('.crazy_cep').mask(mask, options);
        }});
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
        $('.cpf').mask('000.000.000-00', {reverse: true});
        $('.money').mask('#.##0,00', {reverse: true});
        var SPMaskBehavior = function(val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        spOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            }
        };
        $('.sp_celphones').mask(SPMaskBehavior, spOptions);
        $('pre').each(function(i, e) {hljs.highlightBlock(e)});
    }
    $(document).ready(function() {
        applyMasks();
    });
    $(document).on('DOMSubtreeModified', function() {
        applyMasks();
    });
})(jQuery);
//-----------------------------------------------------------------------------------

//Funções
$(document).ready(function() {
    //Colocar active na class automáticamente caso for igual a página com o href
    $('.sidebar ul li a').each(function() {
        var linkBase = $(this).attr('href');
        if(currentPageBase.startsWith(linkBase)) {
            $(this).addClass('active');
        }
    });
    $('.topbar ul:first-child li a').each(function() {
        var linkBase = $(this).attr('href');
        if(currentPageBase.startsWith(linkBase)) {
            $(this).addClass('active');
        }
    });
    //-----------------------------------------------------------------------------------

    //Abrir sidebar no mobile
    $('body').on('click', '.sidebar_toggle', function() {
        $('body').append('<div class="modal-backdrop fade show"></div>');
        $('.sidebar').toggle();
    });
    //-----------------------------------------------------------------------------------

    //Fechar sidebar ao clicar no modal backdrop
    $('body').on('click', '.modal-backdrop', function() {
        $(this).remove();
        $('.sidebar').hide();
    });
    //-----------------------------------------------------------------------------------

    //Ajaxs
    $(document).on('submit', 'form[method="POST"]', function(e) {
        var this_form = $(this);
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: PATH + 'ajaxs/',
            data: new FormData(this),
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                this_form.append('<div class="loading"><div class="spinner-border text-dark"></div></div>');
            },
            success: function(data) {
                $('.alert, .loading').remove();
                if(data.sucesso != null) {
                    if(data.sucesso != '') {
                        this_form.prepend('<div class="alert alert-success alert-dismissible fade show"><span><strong>Ooba!</strong> '+ data.sucesso +'</span><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    }
                    if(data.atualizar != null) {
                        if(data.atualizar != 'direto') {
                            setTimeout(() => {
                                if(data.redirecionamento != null) {
                                    window.location.href = data.redirecionamento;
                                }
                                else {
                                    location.reload();
                                }
                            }, 1500);
                        }
                        else {
                            if(data.redirecionamento != null) {
                                window.location.href = data.redirecionamento;
                            }
                            else {
                                location.reload();
                            }
                        }
                    }
                }
                else if(data.erro != null) {
                    this_form.prepend('<div class="alert alert-danger alert-dismissible fade show"><span><strong>Oops!</strong> '+ data.erro +'</span><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                }
            }
        });
    });
    //-----------------------------------------------------------------------------------

    //Sair da conta
    $('.sair').click(function() {
        $.ajax({
            url: PATH + 'ajaxs/',
            method: 'POST',
            dataType: 'JSON',
            data: {
                sair: ''
            },
            success: function(data) {
                if(data.sucesso != null) {
                    window.location.href = PATH;
                }
            }
        });
    });
    //-----------------------------------------------------------------------------------

    //Textarea auto height
    function ajustarAlturaTextarea(textarea) {
        textarea.style.height = '20px';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }
    var $textareas = $('textarea');
    $textareas.each(function() {
        if(!$(this).attr('style') || !$(this).attr('style').includes('height')) {
            ajustarAlturaTextarea(this);
        }
    });
    $(document).on('input', 'textarea', function() {
        ajustarAlturaTextarea(this);
    });
    //-----------------------------------------------------------------------------------

    //Ordenação
    $(function() {
        $('.sortable').sortable({
            axis: 'x,y',
            update: function(event, ui) {
                var id = $(this).attr('id');
                var ordem = $(this).sortable('toArray');
                $.ajax({
                    url: PATH + 'ajaxs/',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: id,
                        ordem: ordem
                    }
                });
            }
        });
    });
    //-----------------------------------------------------------------------------------

    //Máscara CPF ou CNPJ
    $('.cpf_cnpj').keypress(function() {
        var tamanho = $(this).val().length;
        if(tamanho <= 13) {
            $(this).mask('000.000.000-00', {reverse: true});
        }
        else {
            $(this).mask('00.000.000/0000-00', {reverse: true});
        }                   
    });
    //-----------------------------------------------------------------------------------
});
//-----------------------------------------------------------------------------------

//Agenda
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('agenda');
    if(calendarEl) {
        if(window.innerWidth <= 991) {
            var initialView = 'listDay';
        }
        else {
            var initialView = 'dayGridMonth';
        }
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: initialView,
            allDaySlot: false,
            headerToolbar: {
                start: 'prev,next today',
                center: 'title',
                end: 'dayGridMonth,listDay'
            },
            locale: 'pt-br',
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
                list: 'Lista'
            },
            events: {
                url: PATH + 'ajaxs/',
                method: 'POST',
                extraParams: {
                    agenda: true
                }
            },
            dateClick: function(info) {
                $('#dateclick').remove();
                const date = info.date;
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const data = `${day}/${month}/${year}`;
                const modal = document.createElement('div');
                modal.id = 'dateclick';
                modal.classList.add('modal', 'fade');
                modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cadastrar novo Evento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" id="titulo" class="form-control" placeholder="Digite um título" name="titulo" required>
                                    <label for="titulo">Título</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea id="descricao" class="form-control" placeholder="Digite uma descrição" name="descricao" style="height: 100px;"></textarea>
                                    <label for="descricao">Descrição</label>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="data_inicial" class="form-control date" placeholder="Digite a data inicial" name="data_inicial" value="${data}" required>
                                            <label for="data_inicial">Data Inicial</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="horario_inicial" class="form-control time" placeholder="Digite o horário inicial" name="horario_inicial" required>
                                            <label for="horario_inicial">Horário Inicial</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="data_final" class="form-control date" placeholder="Digite a data final" name="data_final" value="${data}" required>
                                            <label for="data_final">Data Final</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="horario_final" class="form-control time" placeholder="Digite o horário final" name="horario_final" required>
                                            <label for="horario_final">Horário Final</label>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="agendar">
                                <button type="submit" class="w-100">AGENDAR <i class="fa-solid fa-circle-plus"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                `;
                document.body.appendChild(modal);
                new bootstrap.Modal(modal).show();
            },
            eventContent: function(arg) {
                const startTime = arg.event.start.toLocaleTimeString([], {
                    hour: '2-digit', minute: '2-digit'
                });
                const eventTitle = `${arg.event.title} ás ${startTime}hs`;
                const content = document.createElement('div');
                content.innerHTML = eventTitle;
                return {
                    domNodes: [content]
                };
            },
            eventClick: function(info) {
                const modal = document.createElement('div');
                modal.id = 'detalhes';
                modal.classList.add('modal', 'fade');
                if(info.event.extendedProps.description != '') {
                    var description = `<p class="mb-3">${info.event.extendedProps.description}</p>`;
                }
                else {
                    var description = '';
                }
                modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${info.event.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${description}
                            <div class="d-flex gap-2">
                                <a data-bs-toggle="modal" data-bs-target="#atualizar_agenda"><i class="fa-solid fa-edit"></i> Editar</a>
                                <a data-bs-toggle="modal" data-bs-target="#excluir_agenda"><i class="fa-solid fa-trash"></i> Excluir</a>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                const segundoModal = document.createElement('div');
                segundoModal.classList.add('modal', 'fade');
                segundoModal.id = 'atualizar_agenda';
                segundoModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar: ${info.event.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-floating mb-3">
                                    <input type="text" id="titulo" class="form-control" placeholder="Digite um título" name="titulo" value="${info.event.title}" required>
                                    <label for="titulo">Título</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea id="descricao" class="form-control" placeholder="Digite uma descrição" name="descricao" style="height: 100px;">${info.event.extendedProps.description}</textarea>
                                    <label for="descricao">Descrição</label>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="data_inicial" class="form-control date" placeholder="Digite a data inicial" name="data_inicial" value="${info.event.extendedProps.data_inicial}" required>
                                            <label for="data_inicial">Data Inicial</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="horario_inicial" class="form-control time" placeholder="Digite o horário inicial" name="horario_inicial" value="${info.event.extendedProps.horario_inicial}" required>
                                            <label for="horario_inicial">Horário Inicial</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="data_final" class="form-control date" placeholder="Digite a data final" name="data_final" value="${info.event.extendedProps.data_final}" required>
                                            <label for="data_final">Data Final</label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-floating">
                                            <input type="text" id="horario_final" class="form-control time" placeholder="Digite o horário final" name="horario_final" value="${info.event.extendedProps.horario_final}" required>
                                            <label for="horario_final">Horário Final</label>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="atualizar_agenda" value="${info.event.id}">
                                <button type="submit" class="w-100">SALVAR <i class="fa-solid fa-floppy-disk"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                `;
                const terceiroModal = document.createElement('div');
                terceiroModal.classList.add('modal', 'fade');
                terceiroModal.id = 'excluir_agenda';
                terceiroModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Excluir: ${info.event.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="excluir_agenda" value="${info.event.id}">
                                <button type="submit" class="w-100 bg-danger border-danger">CONFIRMAR EXCLUSÃO <i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                `;
                document.body.appendChild(modal);
                document.body.appendChild(segundoModal);
                document.body.appendChild(terceiroModal);
                new bootstrap.Modal($('#detalhes')).show();
            }
        });
        /*
        var selectMonth = 0;
        var selectYear = 2023;
        var selectedDate = new Date(selectYear, selectMonth, 1);
        calendar.gotoDate(selectedDate);
        */
        calendar.render();
    }
});
//-----------------------------------------------------------------------------------

//Table responsiva
$(window).on('load resize', function() {
    if(window.innerWidth <= 768) {
        $('table thead tr th[class*="hide_mobile"], table tbody tr td[class*="hide_mobile"]').hide();
        $('tbody tr').click(function() {
            const tds = $(this).find('td');
            let tds_conteudos = '';
            tds.each(function(index) {
                if(index > 0) {
                    tds_conteudos += '<p><strong>' + $('thead th').eq(index).html() + ':</strong></p><p>' + $(this).html() + '</p>';
                    tds_conteudos += '<hr>';
                }
            });
            const modal = document.createElement('div');
            modal.id = 'detalhes';
            modal.classList.add('modal', 'fade');
            modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${$(this).find('td:nth-child(1)').text()}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${tds_conteudos}
                    </div>
                </div>
            </div>
            `;
            document.body.appendChild(modal);
            new bootstrap.Modal($('#detalhes')).show();
        });
    }
    else {
        $('table thead tr th[class*="hide_mobile"], table tbody tr td[class*="hide_mobile"]').show();
    }
});
//-----------------------------------------------------------------------------------
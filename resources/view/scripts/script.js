// Avoid page resubmission on refresh
// O código abaixo evitar reenviar o mesmo formulário HTML ao fazer refresh na página
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
  
  /* 
  
  NOTA: Se o código começar com $(). Significa que está sendo utilizado Jquery.
  é uma biblioteca livre que contém funções da linguagem de programação JavaScript que interage com páginas em HTML,
  desenvolvida para simplificar os scripts executados/interpretados no navegador de internet do usuário.
  
  */
  
  // Datatables é uma biblioteca Javascript de aprimoramento de tabelas HTML.
  
  $('#notaTable').DataTable({
    "language": {
      "url": "https://cdn.datatables.net/plug-ins/2.0.3/i18n/pt-BR.json",
    },
    "columnDefs": [
      { "orderable": false, "targets": 5 }
    ]
  });
  
  /*
  
  Informações sobre o AJAX:
    https://www.devmedia.com.br/o-que-e-o-ajax/6702
    https://www.hostinger.com.br/tutoriais/o-que-e-ajax
  
  */
  
  // Espera a página ter terminado de carregar
  $(document).ready(function () {
  
    // Se um link for clicado
    $('a').click(function () {
  
      // Verifica se o link tem a classe edit
      if ($(this).hasClass("edit")) {
  
        // Pega o texto do parente, parente, e localiza a primeira td (table cell) e remove os espaços.
        var id = $(this).parent().parent().find("td:first").text().trim();
  
        // AJAX
        $.ajax({
          url: './fetchgrade.php',
          type: 'POST',
          data: { id: id },
  
          success: function (output) {
            data = JSON.parse(output);
            $('#editId').val(data['id']);
            $('#disciplina').val(data['disciplina']);
            $('#nota1').val(data['n1']);
            $('#nota2').val(data['n2']);
            $('#notafinal').val(data['notafinal']);
          }
        });
      } else if ($(this).hasClass("delete")) {
  
        var id = $(this).parent().parent().find("td:first").text().trim();
  
        $('#deleteConfirmar').click(function () {
  
          // AJAX
          $.ajax({
            url: './deletegrade.php',
            type: 'POST',
            data: { id: id },
    
            success: function (output) {
              window.location.href = "./index.php";
            }
          });
  
        });
  
      }
  
    });
  
  });
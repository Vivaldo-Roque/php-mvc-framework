/* 
 
NOTA: Se o código começar com $(). Significa que está sendo utilizado Jquery.
é uma biblioteca livre que contém funções da linguagem de programação JavaScript que interage com páginas em HTML,
desenvolvida para simplificar os scripts executados/interpretados no navegador de internet do usuário.
 
*/

// Espera a página ter terminado de carregar
$(document).ready(function () {

  // Avoid page resubmission on refresh
  // O código abaixo evitar reenviar o mesmo formulário HTML ao fazer refresh na página
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  console.log("Hello world!");
  
});


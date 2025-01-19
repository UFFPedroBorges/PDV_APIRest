document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.menu-link');

    links.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault(); // Evita o comportamento padrão do link

            const page = event.target.getAttribute('data-page'); // Obtém a página

			//Faz o corregamento da pagina
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar o conteúdo.');
                    }
                    return response.text();
                })
                .then(html => {
                    // Insere o conteúdo carregado na área principal
                    document.getElementById('content').innerHTML = html;
                })
                .catch(error => {
                    // Exibe uma mensagem de erro
                    document.getElementById('content').innerHTML = `<p class="error-message">Erro: ${error.message}</p>`;
                });
        });
    });
});

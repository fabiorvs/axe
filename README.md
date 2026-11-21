<h1>AXE CMS</h1>

> Este projeto é um fork do [Axe CMS](http://augustocampos.net/axe/blog/), criado por Augusto Campos. Mantém a proposta original de gerar blogs estáticos em PHP, com melhorias de compatibilidade com PHP atual, automação de build e validação, uma configuração local de desenvolvimento e o tema Lumen.

O código original permanece sob a licença Apache 2.0. Consulte o histórico e a licença para os créditos completos.

*ATUALIZAÇÃO EM 6.5.2026*

Doze anos depois, o Axe ainda continua em uso, e sua base de código permanece a mesma desde a versão que disponibilizei aqui em 2013, exceto quanto a pequenas atualizações demandadas pela evolução da sintaxe do PHP em modo shell.

Este repositório no Github não será mais mantido, e as comunicações nele não serão mais monitoradas. O código-fonte publicado aqui vai permanecer sendo o de 2013.

Em compensação, disponibilizei hoje o download de uma versão atualizada do pacote tar de instalação e código-fonte, no próprio <a href="http://augustocampos.net/axe/blog/">site do Axe</a>. É quase igual à versão de 2013, com alteração apenas em 2 arquivos PHP, descritos por lá.

Um abraço a todos,

Augusto Campos

----

## Sobre o Axe


O Axe é um sistema gerenciador de conteúdo estático especializado na geração de blogs. Em outras palavras: o Axe é um sistema em PHP que, a partir de arquivos contendo apenas o título e o corpo do post, em formato texto ou HTML, gera toda a estrutura típica de um blog: índices, feed, tags, sitemap, etc.

As atividades de administração do Axe ocorrem via linha de comando no Terminal, portanto ele exige que você tenha acesso shell (SSH ou similar) ao seu servidor de hospedagem e saiba usá-lo. Ele também exige que o PHP esteja disponível na linha de comando, embora dispense a ativação do mod_php no servidor web.

Reinventar a roda pode não ser uma solução eficiente (às vezes nem eficaz), mas é divertido, estimula o aprendizado e, afinal de contas, você pode terminar o processo com uma roda exatamente do jeito que desejava, feita por você.

<img src="http://static.efetividade.net/img/tela-darwin-2013-05-09-702-253990.jpg">

O Axe é a minha mais recente reinvenção de roda, porque é um sistema de gerenciador de conteúdo de blogs baseado em arquivos estáticos e sem precisar de bancos de dados, como já existem muitos por aí. O que ele faz de bom? Veja nos links abaixo:

<ul>
<li><a href="http://augustocampos.net/axe/blog/2013/06/o-que-e-o-axe.html">O que é o axe</a> (inclui FAQ)
<li><a href="http://augustocampos.net/axe/blog/">Blog do axe</a>
</ul>

O Axe está disponível sob a licença Apache 2.0.

## Desenvolvimento local

Copie `axe/axe_config_exemplo.php` para `axe/axe_config.php` e ajuste os caminhos.
Os posts novos devem ficar em `content/`, em Markdown com front matter. Para gerar o site
e os assets do tema, execute `./scripts/build.sh`. Para validar a saída, execute
`./scripts/validate.sh`; para testar que HTML de conteúdo é escapado, execute
`./tests/safe-build.sh`.

O build padrão não executa conteúdo nem plugins PHP. Para migrar um descriptor legado
estático, use `php scripts/migrate-legacy-post.php origem.php destino.md`; revise o
Markdown gerado antes de publicá-lo. O servidor deve expor somente `public/` em produção;
nunca exponha os diretórios `axe/`, `axethemes/` ou o arquivo de configuração.

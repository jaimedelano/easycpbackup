Código-fonte do Enhanced Backup v4

Recursos incluídos no aplicativo.   

        Verificação de vírus após download
        Migração de revenda inteiras e contas individuais (exportação para servidor remoto ou importação para servidor local)
        Barra de progresso indicando dados a serem transferidos, velocidade de download e tempo restante
        Sistema de compressão PigZ (melhor utilização da CPU)
        Sistema de monitoramente e limitação de recursos: defina o máximo de memória, I/O ou CPU que cada operação pode usar
        Limitação de espaço, tráfego e operações de cada usuário do aplicativo
        Sistema de cache para agilizar procedimentos repetitivos
        Sistema de estatística de uso, progresso e desempenho de cada operação e de cada usuário
        Sistema de análise de logs e correção de erros
        Versionamento de arquivos, emails e bancos de dados: mantenha cópia de diferentes horas, dias ou semanas
        Suporte a múltiplos idiomas
        API para integração com outros sistemas
        Realizar mais de uma operação de migração, backup ou restauração ao mesmo tempo
        Download e upload de e para o computador local

Os módulos possuem recursos adicionais como seguem abaixo:

        Sistema de backup pseudo ou quase-incremental sobre FTP: somente é copiado o que sofreu edição
        Interface mobile para aparelhos móveis
        Cópia de arquivo sempre que alguma alteração é feita
        Importação de contas do WHMSonic: migração de servidor remoto para local, de contas individuais e de revendas inteiras
        Suporte a múltiplos servidores: gerencie backup e im/exportação de contas de vários servidores com uma única instalação do aplicativo
        Importação de contas do Plesk e de painéis genéricos: arquivos (FTP), bancos de dados (MySQL) e emails (SMTP)
        Criação de clone de revendas (cópia idêntica para ser mantida em servidor diferente)
        Compressão transparente de dados: diminua ao máximo o espaço ocupado pelos arquivos (requer storage com acesso root)
        Suporte ao storage de dados Amazon S3
        Suporte ao Google Drive

Também planejo criar uma linguagem de script para ser usada para customização de qualquer tarefa do aplicativo: muito mais personalizável do que via API
Exemplo: para se fazer backup e enviar um SMS após a operação ser concluída, bastaria executar ou agendar:

#!/bin/env tupi

# Quantidade de memória e de CPU  que se pode usar
%max_cpu_usage=30%
%max_mem_usage=20%

# Faz backup do servidor local e envia para servidor FTP remoto
$backup = new Backup([proto => 'ftp', storage => 'usuario:s3nh4@dominio.com']);
$backup->execute('22:30'); # executa às 22h 30min ou $backup->execute(+600), começa em 10 min
new SMS('+55 14 9924 23**', !$backup->had_success ? "Alguma coisa deu errado:\n" . $backup->report : "Tudo OK!\n");

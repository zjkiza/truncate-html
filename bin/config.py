container_php: str = 'php_truncate_html'
container_db: str =  None

waiting_db_connection: bool = True
phpunit_code_error_bypass: bool = False

containers: list = [
    container_php,
    container_db
]

container_work_dir: str = '/www'

docker_compose_files_list: list[str] = [
    'docker-compose.yaml'
]

commands: dict[str, str] = {
    'composer update': 'composer update --no-interaction --prefer-dist',
    'composer validate': 'composer validate',
    'composer composer-unused': 'composer composer-unused',
    'composer run phpunit': 'composer run phpunit-ci',
    'composer run phpstan': 'composer run phpstan',
    'composer run psalm': 'composer run psalm',
    'composer run rector': 'composer run rector-check',
    'composer run php-cs-fixer': 'composer run php-cs-fixer-check',
}

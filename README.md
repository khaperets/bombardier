* Редактируем файл `targets.json` с целями для DDoS.
* Запускаем атаку в кубер-кластере:
    ```bash
    ./run_all_kuber.sh
    ```

#### Доступные опции в файле targets.json
* url: URL атаки
* method: GET (по умолчанию), POST, PUT, PATCH, DELETE
* body: список данных для тела запроса
* headers: список HTTP-заголовков
* duration: продолжительность атаки
* connections: к-во одновременных соединений
* replicas: к-во реплик

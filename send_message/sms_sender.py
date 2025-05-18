import pymysql
from twilio.rest import Client
import datetime

# Configurações banco de dados
host = '127.0.0.1'
port = 3307
user = 'novousuario'
password = 'senhaSegura123'
database = 'novobanco'

# Config Twilio
account_sid = ''
auth_token = ''
twilio_number = '+13203968566'

# Cria cliente Twilio
client = Client(account_sid, auth_token)

try:
    # Conexão com banco
    conn = pymysql.connect(
        host=host,
        port=port,
        user=user,
        password=password,
        database=database,
        cursorclass=pymysql.cursors.DictCursor
    )

    with conn.cursor() as cursor:
        print("Executando consulta SQL...")
        now = datetime.datetime.now()
        now_str = now.strftime('00:00:00')

        sql = "SELECT m.nome_medicamento, m.dosagem, u.nome, u.celular FROM medicamento m JOIN usuarios u ON m.id_usuario = u.id_user WHERE m.horario = %s"
        cursor.execute(sql, (now_str,))
        results = cursor.fetchall()

        for row in results:
            nome_medicamento = row['nome_medicamento']
            dosagem = row['dosagem']
            nome_usuario = row['nome']
            celular = "+55" + row['celular']

            mensagem = f"Olá {nome_usuario}, é hora de tomar seu remédio: {nome_medicamento}, dosagem: {dosagem}."
            print(f"Enviando mensagem...")
            message = client.messages.create(
                body=mensagem,
                from_=twilio_number,
                to=celular
            )

            print(f"Mensagem enviada para {nome_usuario} ({celular}) - SID: {message.sid}")

except Exception as e:
    print("Erro:", e)

finally:
    if conn:
        conn.close()

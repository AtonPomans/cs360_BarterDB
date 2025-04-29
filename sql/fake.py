import mysql.connector
import random
from faker import Faker
from faker_commerce import Provider

# Database connection settings
db_host = "localhost"
db_name = "barter_db"
db_user = "root"
db_pass = ""  # Add password if needed

# Connect to MySQL
try:
    conn = mysql.connector.connect(
        host=db_host,
        user=db_user,
        password=db_pass,
        database=db_name
    )
    cur = conn.cursor()
    print("O Connected to MySQL database")
except mysql.connector.Error as err:
    print("X Connection failed:", err)
    exit()

fake = Faker()
fake.add_provider(Provider)

# Insert fake items without user_id
for _ in range(50):  # You can change the number of items
    item_name = fake.ecommerce_name().capitalize()
    item_desc = fake.text(max_nb_chars=150)
    item_value = (random.randint(1, 10000))/100
    try:
        cur.execute(
            "INSERT INTO items (name, description, value) VALUES (%s, %s, %s)",
            (item_name, item_desc, item_value)
        )
    except mysql.connector.Error as err:
        print("X Failed to insert item:", err)

conn.commit()
print("O Inserted fake items into 'items' table successfully.")

# Close connection
cur.close()
conn.close()


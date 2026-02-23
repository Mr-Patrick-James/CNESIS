import pandas as pd
import mysql.connector
import os
import datetime

file_path = r"c:\wamp64\www\CNESIS\assets\list\LIST-OF-ENROLLED-FOR-2ND-SEM-2025-26.xlsx"

db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'cnesis_db'
}

def to_date_str(value):
    if pd.isna(value):
        return None
    if isinstance(value, datetime.datetime):
        return value.date().isoformat()
    if isinstance(value, datetime.date):
        return value.isoformat()
    try:
        dt = pd.to_datetime(value, dayfirst=True, errors='coerce')
        if pd.isna(dt):
            return None
        return dt.date().isoformat()
    except Exception:
        return None

def run_import():
    if not os.path.exists(file_path):
        print(f"File not found: {file_path}")
        return
    xl = pd.ExcelFile(file_path)
    print(f"Sheets: {xl.sheet_names}")

    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()

    total_updates = 0
    for sheet_name in xl.sheet_names:
        preview = pd.read_excel(file_path, sheet_name=sheet_name, nrows=20, header=None)
        header_row = -1
        id_col_idx = -1
        date_col_idx = -1
        remarks_col_idx = -1
        for r_idx, row in preview.iterrows():
            for c_idx, cell in enumerate(row):
                s = str(cell).strip().lower()
                if s == "id number":
                    header_row = r_idx
                    id_col_idx = c_idx
                if s == "date":
                    date_col_idx = c_idx
                if s == "remarks":
                    remarks_col_idx = c_idx
            if header_row != -1:
                break
        if header_row == -1 or id_col_idx == -1:
            print(f"Skipping '{sheet_name}': header not found")
            continue
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=header_row)
        id_col = df.columns[id_col_idx]
        date_col = df.columns[date_col_idx] if date_col_idx != -1 else None
        remarks_col = df.columns[remarks_col_idx] if remarks_col_idx != -1 else None

        sheet_updates = 0
        for _, row in df.iterrows():
            sid = str(row.get(id_col, "")).strip()
            if not sid or sid.lower() == "nan":
                continue
            date_val = to_date_str(row.get(date_col)) if date_col else None
            remarks_val = row.get(remarks_col)
            if pd.isna(remarks_val):
                remarks_val = None
            else:
                remarks_val = str(remarks_val).strip()
                if remarks_val == "":
                    remarks_val = None
            cursor.execute(
                "UPDATE students SET date_enrolled=%s, remarks=%s WHERE student_id=%s",
                (date_val, remarks_val, sid)
            )
            if cursor.rowcount > 0:
                sheet_updates += 1
        conn.commit()
        total_updates += sheet_updates
        print(f"{sheet_updates} updates in '{sheet_name}'")

    conn.close()
    print(f"Total updates: {total_updates}")

if __name__ == "__main__":
    run_import()

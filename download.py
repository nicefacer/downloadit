import os
import stat
import paramiko

SFTP_HOST = os.environ["SFTP_HOST"]
SFTP_PORT = int(os.environ.get("SFTP_PORT", 22))
SFTP_USER = os.environ["SFTP_USER"]
SFTP_PASS = os.environ["SFTP_PASS"]
REMOTE_BASE_DIR = os.environ.get("REMOTE_DIR", "/")  # base remote dir
LOCAL_BASE_DIR = os.getcwd()

DONE_LIST = ".downloaded_files.txt"

def load_downloaded_files():
    if os.path.exists(DONE_LIST):
        with open(DONE_LIST, "r") as f:
            return set(line.strip() for line in f.readlines())
    return set()

def save_downloaded_file(remote_path):
    with open(DONE_LIST, "a") as f:
        f.write(f"{remote_path}\n")

def download_recursive(sftp, remote_dir, local_dir, downloaded_files):
    os.makedirs(local_dir, exist_ok=True)

    for entry in sftp.listdir_attr(remote_dir):
        remote_path = f"{remote_dir}/{entry.filename}"
        local_path = os.path.join(local_dir, entry.filename)

        if remote_path in downloaded_files:
            print(f"Skipping: {remote_path}")
            continue

        if stat.S_ISDIR(entry.st_mode):
            download_recursive(sftp, remote_path, local_path, downloaded_files)
        else:
            print(f"Downloading: {remote_path} -> {local_path}")
            try:
                sftp.get(remote_path, local_path)
                save_downloaded_file(remote_path)
            except Exception as e:
                print(f"Failed to download {remote_path}: {e}")
                return  # Exit and retry next time

def download_all():
    transport = paramiko.Transport((SFTP_HOST, SFTP_PORT))
    transport.connect(username=SFTP_USER, password=SFTP_PASS)
    sftp = paramiko.SFTPClient.from_transport(transport)

    downloaded_files = load_downloaded_files()
    download_recursive(sftp, REMOTE_BASE_DIR, LOCAL_BASE_DIR, downloaded_files)

    sftp.close()
    transport.close()

if __name__ == "__main__":
    download_all()

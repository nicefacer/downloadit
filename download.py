import os
import stat
import paramiko
import sys

# Try to load .env file if it exists (for local development)
if os.path.exists('.env'):
    with open('.env', 'r') as f:
        for line in f:
            line = line.strip()
            if line and not line.startswith('#') and '=' in line:
                key, value = line.split('=', 1)
                os.environ[key] = value

# Check for required environment variables
required_vars = ["SFTP_HOST", "SFTP_USER", "SFTP_PASS"]
missing_vars = [var for var in required_vars if var not in os.environ]

if missing_vars:
    print(f"Error: Missing required environment variables: {', '.join(missing_vars)}")
    print("Please set these environment variables before running the script.")
    print("Example:")
    print("export SFTP_HOST=your_host")
    print("export SFTP_USER=your_username")
    print("export SFTP_PASS=your_password")
    print("export SFTP_PORT=22  # optional, defaults to 22")
    print("export REMOTE_DIR=/  # optional, defaults to /")
    print("export LOCAL_DIR=./store  # optional, defaults to ./store")
    sys.exit(1)

SFTP_HOST = os.environ["SFTP_HOST"]
SFTP_PORT = int(os.environ.get("SFTP_PORT", 22))
SFTP_USER = os.environ["SFTP_USER"]
SFTP_PASS = os.environ["SFTP_PASS"]
REMOTE_BASE_DIR = os.environ.get("REMOTE_DIR", "/")  # base remote dir
LOCAL_BASE_DIR = os.environ.get("LOCAL_DIR", os.path.join(os.getcwd(), "store"))

# List of specific directories to download
TARGET_DIRECTORIES = [
    "ProviderRestore",
    "_ProviderRestore_db6...", 
    "Adapter",
    "adminlinks64",
    "cache",
    "cache0",
    "cgi-bin",
    "classes",
    "config",
    "controllers",
    "Core",
    "css",
    "docs",
    "download"
    
]

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
    print("Connecting to:", SFTP_HOST, SFTP_PORT)
    transport = paramiko.Transport((SFTP_HOST, SFTP_PORT))
    transport.connect(username=SFTP_USER, password=SFTP_PASS)
    sftp = paramiko.SFTPClient.from_transport(transport)

    downloaded_files = load_downloaded_files()
    
    print(f"Target directories to download: {TARGET_DIRECTORIES}")
    
    # Download each target directory
    for target_dir in TARGET_DIRECTORIES:
        remote_path = f"{REMOTE_BASE_DIR.rstrip('/')}/{target_dir}"
        local_path = os.path.join(LOCAL_BASE_DIR, target_dir)
        
        try:
            # Check if directory exists on remote server
            sftp.listdir(remote_path)
            print(f"Downloading directory: {remote_path} -> {local_path}")
            download_recursive(sftp, remote_path, local_path, downloaded_files)
        except FileNotFoundError:
            print(f"Directory not found on server: {remote_path}")
        except Exception as e:
            print(f"Error accessing directory {remote_path}: {e}")

    sftp.close()
    transport.close()

if __name__ == "__main__":
    download_all()

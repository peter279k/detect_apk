# detect_apk
help you get the apk informations
# Usage
## Step1
Download composer.phar ans use following command:

```bash
php cimposer.phar install
```

## Step2
Create the db.txt

```
your-user-name
your-password
```

## Step3
Download the [apktools](https://github.com/iBotPeaches/Apktool) because the aapt package is in the project.

## Step4
Edit the file_path.json and you can create many file_path values in the key name: file_path

Specify the current and absolute file path for aapt execution file.

```
{
    "file_path": [
		"your apk file path1", 
		"your apk file path2", 
		"your apk file path3" 
	], 
	"aapt_execution": "C:\\Apktool\\brut.apktool\\apktool-lib\\src\\main\\resources\\prebuilt\\aapt\\windows\\aapt.exe"
}

```
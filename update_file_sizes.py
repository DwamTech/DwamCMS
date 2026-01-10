import os
import re

# Define the base directory
base_dir = r"e:\Work\Code\Dwam Projects\Dwam_CMS\FinalBackVersion\app\Http\Controllers"

# Pattern to match file/image validations with max size
pattern = re.compile(r"(\|max:)(\d{3,})")

# Replacement function
def replace_max_size(match):
    return match.group(1) + "1048576"

# Counter for updated files
updated_count = 0

# Walk through all PHP files
for root, dirs, files in os.walk(base_dir):
    for file in files:
        if file.endswith('.php'):
            file_path = os.path.join(root, file)
            
            try:
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Apply regex replacement
                new_content = pattern.sub(replace_max_size, content)
                
                # Only write if changes were made
                if new_content != content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f"Updated: {file_path}")
                    updated_count += 1
            except Exception as e:
                print(f"Error processing {file_path}: {e}")

print(f"\nTotal files updated: {updated_count}")

<?php
include("config.php");

// Create tokens table if not exists
function createTokensTable() {
    global $conn;
    $sql = "CREATE TABLE IF NOT EXISTS tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        original_text TEXT NOT NULL,
        tokens JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    return mysqli_query($conn, $sql);
}

// Tokenize input text
function tokenizeText($text) {
    // Basic tokenization: split by spaces and punctuation
    $tokens = preg_split('/[\s\p{P}]+/u', strtolower(trim($text)), -1, PREG_SPLIT_NO_EMPTY);
    return array_values(array_unique($tokens));
}

// Store tokenized input in database
function storeTokens($text) {
    global $conn;
    
    createTokensTable();
    
    $tokens = tokenizeText($text);
    $tokensJson = json_encode($tokens);
    
    $data = [
        'original_text' => $text,
        'tokens' => $tokensJson
    ];
    
    return insertInto('tokens', $data, true);
}

// Handle form submission
if ($_POST['action'] == 'tokenize' && !empty($_POST['input_text'])) {
    $text = $_POST['input_text'];
    $id = storeTokens($text);
    
    if ($id) {
        $tokens = tokenizeText($text);
        $response = [
            'success' => true,
            'id' => $id,
            'tokens' => $tokens,
            'count' => count($tokens)
        ];
    } else {
        $response = ['success' => false, 'error' => 'Failed to store tokens'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Tokenizer - Lost Nest</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tokenizer-container { max-width: 800px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; color: #004A99; font-weight: bold; }
        .form-group textarea { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; font-size: 14px; }
        .tokenize-btn { background: #004A99; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .tokenize-btn:hover { background: #1E64C8; }
        .result { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .token { display: inline-block; background: #004A99; color: white; padding: 5px 10px; margin: 3px; border-radius: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="tokenizer-container">
        <h2 style="color: #004A99; text-align: center;">Text Tokenizer</h2>
        
        <form id="tokenizerForm">
            <div class="form-group">
                <label for="input_text">Enter text to tokenize:</label>
                <textarea id="input_text" name="input_text" rows="5" placeholder="Enter your text here..." required></textarea>
            </div>
            <button type="submit" class="tokenize-btn">Tokenize & Store</button>
        </form>
        
        <div id="result" class="result" style="display: none;"></div>
    </div>

    <script>
    document.getElementById('tokenizerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('action', 'tokenize');
        formData.append('input_text', document.getElementById('input_text').value);
        
        fetch('tokenizer.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            
            if (data.success) {
                let tokensHtml = data.tokens.map(token => `<span class="token">${token}</span>`).join('');
                resultDiv.innerHTML = `
                    <h4>Tokenization Result (ID: ${data.id})</h4>
                    <p><strong>Token Count:</strong> ${data.count}</p>
                    <div><strong>Tokens:</strong><br>${tokensHtml}</div>
                `;
            } else {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
            }
            
            resultDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = '<p style="color: red;">An error occurred</p>';
            document.getElementById('result').style.display = 'block';
        });
    });
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>
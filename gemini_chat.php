<?php
// gemini_chat.php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userInput = $data['message'] ?? '';

$API_KEY = 'AIzaSyB79cDg-jncTNjXizh12Y7IFH5Mu__sWJo'; // Replace with a working API key

$body = [
  'contents' => [
    ['parts' => [['text' => $userInput]]]
  ]
];

$response = sendToGemini($API_KEY, $body);

echo json_encode(['reply' => $response ?? "Sorry, I couldn't respond."]);

function sendToGemini($apiKey, $body) {
  $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $apiKey;

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
  ]);

  $result = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpcode !== 200) {
    return null;
  }

  $response = json_decode($result, true);
  return $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
}
?>

<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$query = $_GET['q'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode(['suggestions' => []]);
    exit;
}

try {
    // Multi-strategy search for typo tolerance
    $searchTerm = "%{$query}%";
    $results = [];
    $seenIds = [];
    
    // Strategy 1: Exact partial match (highest priority)
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.name, 'product' as type, p.id, p.image, 1 as priority
        FROM products p 
        WHERE p.name LIKE ? 
        LIMIT 3
    ");
    $stmt->execute([$searchTerm]);
    $exactMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($exactMatches as $match) {
        $results[] = $match;
        $seenIds[$match['id']] = true;
    }
    
    // Strategy 2: SOUNDEX for phonetic matching (catches sound-alike typos)
    // Only if we have less than 5 results
    if (count($results) < 5 && strlen($query) >= 3) {
        $stmt = $pdo->prepare("
            SELECT DISTINCT p.name, 'product' as type, p.id, p.image, 2 as priority
            FROM products p 
            WHERE SOUNDEX(p.name) = SOUNDEX(?) 
            LIMIT 3
        ");
        $stmt->execute([$query]);
        $soundexMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($soundexMatches as $match) {
            if (!isset($seenIds[$match['id']])) {
                $results[] = $match;
                $seenIds[$match['id']] = true;
            }
        }
    }
    
    // Strategy 3: Individual word matching (handles partial typos)
    if (count($results) < 5) {
        $words = explode(' ', $query);
        foreach ($words as $word) {
            if (strlen($word) >= 3) {
                $wordPattern = "%{$word}%";
                $stmt = $pdo->prepare("
                    SELECT DISTINCT p.name, 'product' as type, p.id, p.image, 3 as priority
                    FROM products p 
                    WHERE p.name LIKE ? 
                    LIMIT 2
                ");
                $stmt->execute([$wordPattern]);
                $wordMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($wordMatches as $match) {
                    if (!isset($seenIds[$match['id']]) && count($results) < 5) {
                        $results[] = $match;
                        $seenIds[$match['id']] = true;
                    }
                }
            }
        }
    }
    
    // Get category suggestions (with typo tolerance)
    $stmt = $pdo->prepare("
        SELECT DISTINCT c.name, 'category' as type, c.id, NULL as image, 1 as priority
        FROM categories c 
        WHERE c.name LIKE ? OR SOUNDEX(c.name) = SOUNDEX(?)
        LIMIT 2
    ");
    $stmt->execute([$searchTerm, $query]);
    $categoryResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine results (products first, then categories)
    $suggestions = array_merge(
        array_slice($results, 0, 5),
        $categoryResults
    );
    
    echo json_encode([
        'suggestions' => $suggestions,
        'query' => $query,
        'cached' => false,
        'strategies_used' => [
            'exact_match',
            'soundex',
            'word_matching'
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Search failed', 'suggestions' => []]);
}
?>

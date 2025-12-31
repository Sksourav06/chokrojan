<?php

use App\Models\Station;

if (!function_exists('stationName')) {
    function stationName($id)
    {
        return Station::find($id)->name ?? 'Unknown';
    }
}
// app/Helpers/ViewHelpers.php

/**
 * Checks if the function renderDeck does not exist before defining it.
 * This prevents the "Cannot redeclare function" fatal error.
 */
if (!function_exists('renderDeck')) {
    /**
     * Renders a custom card or "deck" structure based on the provided data.
     *
     * @param array $data An associative array containing data for the deck.
     * @return string The generated HTML markup for the deck.
     */
    function renderDeck(array $data): string
    {
        // Define default values or ensure keys exist
        $title = $data['title'] ?? 'Default Deck Title';
        $content = $data['content'] ?? 'No content provided.';
        $status = $data['status'] ?? 'info'; // e.g., 'success', 'warning', 'danger'

        // Start building the HTML output
        $html = '<div class="custom-deck custom-deck-' . htmlspecialchars($status) . '">';
        $html .= '    <h3 class="deck-title">' . htmlspecialchars($title) . '</h3>';
        $html .= '    <div class="deck-content">';
        $html .=          $content; // Assuming content might already be HTML or will be safely escaped elsewhere
        $html .= '    </div>';
        $html .= '    <p class="deck-footer">Status: ' . htmlspecialchars(ucfirst($status)) . '</p>';
        $html .= '</div>';

        return $html;
    }
}
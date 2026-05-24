<?php
/**
 * Vstupní bod aplikace VinyLog
 *
 * Tento soubor slouží jako veřejný root aplikace.
 * Okamžitě přesměruje prohlížeč na hlavní controller (seznam vinylů).
 *
 * URL workflow:
 *   /VinyLog/ → index.php → VinylController.php?action=index → vinyl_index.php (view)
 */
header('Location: /WA-2026-Ulrich-Jan/VinyLog/app/controllers/VinylController.php?action=index');
exit;

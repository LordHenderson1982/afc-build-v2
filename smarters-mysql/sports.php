<?php
include('includes/header.php');

// File paths
$countriesJsonPath = "./api/all_countries.json";
$sportsJsonPath = "./api/all_sports.json";
$timezonesJsonPath = "./api/timezones.json";
$cacheFile = "./api/cache/new_sports_cache.json";

$message = '';
$countryData = [];
$sportsData = [];
$timezonesData = [];
$selectedCountries = [];
$selectedSports = [];
$selectedTimezone = '';

// Load the JSON files
try {
    if (file_exists($countriesJsonPath)) {
        $jsonContent = file_get_contents($countriesJsonPath);
        $countryData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON: " . json_last_error_msg());
        }

        $selectedCountries = $countryData['selected_countries'] ?? [];
    } else {
        throw new Exception("JSON file not found: $countriesJsonPath");
    }

    if (file_exists($sportsJsonPath)) {
        $jsonContent = file_get_contents($sportsJsonPath);
        $sportsData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON: " . json_last_error_msg());
        }

        $selectedSports = $sportsData['selected_sports'] ?? [];
    } else {
        throw new Exception("JSON file not found: $sportsJsonPath");
    }

    if (file_exists($timezonesJsonPath)) {
        $jsonContent = file_get_contents($timezonesJsonPath);
        $timezonesData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding JSON: " . json_last_error_msg());
        }

        $selectedTimezone = $timezonesData['selected_timezone'] ?? '';
    } else {
        throw new Exception("JSON file not found: $timezonesJsonPath");
    }
} catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSelectedCountries = $_POST['countries'] ?? [];
    $newSelectedSports = $_POST['sports'] ?? [];
    $newSelectedTimezone = $_POST['timezone'] ?? '';

    try {
        if (empty($newSelectedCountries)) {
            throw new Exception("At least one country must be selected.");
        }
        if (empty($newSelectedSports)) {
            throw new Exception("At least one sport must be selected.");
        }
        if (empty($newSelectedTimezone)) {
            throw new Exception("A time zone must be selected.");
        }

        // Update the selected countries, sports, and time zone in the JSON data
        $countryData['selected_countries'] = $newSelectedCountries;
        $sportsData['selected_sports'] = $newSelectedSports;
        $timezonesData['selected_timezone'] = $newSelectedTimezone;

        // Save the updated JSON back to files
        if (file_put_contents($countriesJsonPath, json_encode($countryData, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Failed to write to countries JSON file");
        }

        if (file_put_contents($sportsJsonPath, json_encode($sportsData, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Failed to write to sports JSON file");
        }

        if (file_put_contents($timezonesJsonPath, json_encode($timezonesData, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Failed to write to timezones JSON file");
        }

        // Delete the cache file
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $message = "Selections updated successfully!";
        $selectedCountries = $newSelectedCountries;
        $selectedSports = $newSelectedSports;
        $selectedTimezone = $newSelectedTimezone;
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}
?>
<style>
.scrollable-container {
    max-height: 200px;
    overflow-y: auto;
}
</style>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h2><i class="fa fa-globe"></i> Select Country, Sport, and Time Zone</h2>
        </div>
        <div class="card-body">
            <?php if ($message) { echo '<div class="alert ' . (strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success') . '">' . htmlspecialchars($message) . '</div>'; } ?>
            <form method="post">
                <div class="form-group">
                    <label for="countries">Select Countries:</label>
                    <div class="select-buttons">
                        <button type="button" id="selectAllCountries" class="btn btn-secondary btn-sm">Select All</button>
                        <button type="button" id="deselectAllCountries" class="btn btn-secondary btn-sm">Deselect All</button>
                    </div>
                    <div class="scrollable-container">
                        <div class="row">
                            <?php foreach ($countryData['countries'] as $country) {
                                $selected = in_array($country['name_en'], $selectedCountries) ? 'checked' : '';
                                echo '<div class="col-md-4"><div class="form-check"><input type="checkbox" class="form-check-input country-checkbox" name="countries[]" value="' . htmlspecialchars($country['name_en']) . '" ' . $selected . '><label class="form-check-label">' . htmlspecialchars($country['name_en']) . '</label></div></div>';
                            } ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sports">Select Sports:</label>
                    <div class="select-buttons">
                        <button type="button" id="selectAllSports" class="btn btn-secondary btn-sm">Select All</button>
                        <button type="button" id="deselectAllSports" class="btn btn-secondary btn-sm">Deselect All</button>
                    </div>
                    <div class="scrollable-container">
                        <div class="row">
                            <?php foreach ($sportsData['sports'] as $sport) {
                                $selected = in_array($sport['strSport'], $selectedSports) ? 'checked' : '';
                                echo '<div class="col-md-4"><div class="form-check"><input type="checkbox" class="form-check-input sport-checkbox" name="sports[]" value="' . htmlspecialchars($sport['strSport']) . '" ' . $selected . '><label class="form-check-label">' . htmlspecialchars($sport['strSport']) . '</label></div></div>';
                            } ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="timezone">Select Time Zone:</label>
                    <select id="timezone" name="timezone" class="form-control" required>
                        <?php foreach ($timezonesData['time_zones'] as $key => $timezone) {
                            $selected = $timezone === $selectedTimezone ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($timezone) . '" ' . $selected . '>' . htmlspecialchars($key) . '</option>';
                        } ?>
                    </select>
                </div>

                <div class="form-group text-center">
                    <button class="btn btn-primary" name="submit" type="submit"><i class="fa fa-check"></i> Update Selections</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('selectAllCountries').addEventListener('click', function() {
        document.querySelectorAll('.country-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('deselectAllCountries').addEventListener('click', function() {
        document.querySelectorAll('.country-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });

    document.getElementById('selectAllSports').addEventListener('click', function() {
        document.querySelectorAll('.sport-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('deselectAllSports').addEventListener('click', function() {
        document.querySelectorAll('.sport-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });
});
</script>

<?php include ('includes/footer.php');?>
<?php 
if(!defined('ABSPATH')) {
  exit;
}

/**
 * Allergenes List Template
 * 
 * @var array $allergies_list
 */

 ?>

<div class="allergens-container">
    <h4 class="allergens-title">אלרגנים</h4>
    <p class="allergens-disclaimer">יש לבחור מתוך הרשימה אלרגנים שיש להוציא מהמוצר<br>במידה ואין צורך, ישנה אפשרות ״ללא אלרגנים״</p>
    <details class="allergies-container">
      <summary>לחצ/י לבחירת אלרגנים</summary>
      <div class="options-list-container">
        <input type="text" name="allergen-search" id="allergen-search" placeholder="&#128269; חיפוש אלרגנים מהרשימה...">
        <input type="hidden" name="allergens-for-product" id="allergens-for-product">
        <div class="allergies-list-wrapper options-list-wrapper">
          <?php foreach ($allergies_list as $idx => $allergy_id) { ?>
            <div class="option-wrapper">
              <input type="checkbox" name="allergen-option" id="allergy-<?php echo ++$idx ?>"
                value="<?php echo get_the_title($allergy_id) ?>" required>
              <label for="allergy-<?php echo $idx ?>">
                <?php echo get_the_title($allergy_id) ?>
              </label>
            </div>
          <?php } ?>
          <div class="option-wrapper">
            <input type="checkbox" name="no-allergens" id="no-allergens" value="ללא אלרגנים נוספים להתייחסות" required>
            <label for="no-allergens">ללא אלרגנים להתייחסות</label>
          </div>
        </div>
        <p class="allergies-disclamier">במידה ואינכם מוצאים את האלרגן המבוקש ברשימה - האלרגן אינו נוכח במוצר זה כלל</p>
        <div class="button-wrapper">
          <button type="button" class="approve-alergens-btn">אישור</button>
        </div>
      </div>
    </details>
  </div>
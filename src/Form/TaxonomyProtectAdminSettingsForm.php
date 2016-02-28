<?php

/**
 * @file
 * Contains \Drupal\taxonomy_protect\Form\TaxonomyProtectAdminSettingsForm.
 */

namespace Drupal\taxonomy_protect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\taxonomy\Entity\Vocabulary;

class TaxonomyProtectAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'taxonomy_protect_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('taxonomy_protect.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['taxonomy_protect.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $vocabularies = Vocabulary::loadMultiple();
    $list = [];
    foreach ($vocabularies as $vocabulary) {
      $list[$vocabulary->id()] = $vocabulary->get('name');
    }
    $form['taxonomy_protect_vocabularies'] = [
      '#type' => 'checkboxes',
      '#title' => t('Vocabularies to protect'),
      '#position' => 'left',
      '#options' => $list,
      '#default_value' => \Drupal::config('taxonomy_protect.settings')->get('taxonomy_protect_vocabularies'),
      '#description' => t('Users will be prevented from deleting selected vocabularies.'),
    ];
    return parent::buildForm($form, $form_state);
  }

}

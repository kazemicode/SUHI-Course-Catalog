<?php 
/**
 * @file
 * Contains \Drupal\multistep\Form\MultistepVAPAForm.
 */

namespace Drupal\multistep\Form;

use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;
use Drupal\Core\Entity;
use Drupal\Core\Url;

class MultistepVAPAForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_vapa_class_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $grad_requirement = 16; // investigate loading dynamically by label
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($grad_requirement);
    $subject = $term->getName();

    $bilingual_taxonomy = 21;
    $fundamentals_taxonomy = 22;
    $ap_taxonomy = 12;
    $grade_level = $this->store->get('grade_level');
    $isELD = $this->store->get('isELD');
    $isFund = $this->store->get('isFund');
    $isAP = $this->store->get('isAP');


  

    $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_graduation_requirement' => $grad_requirement, 
      'field_grade_level'=> $grade_level,
      ]);

  
    
  
    $template = [
      '#theme'=> 'course_list_vapa',
      '#grade'=> $grade_level,
      '#results' => $results,
      '#isELD' => $isELD,
      '#isAP' => $isAP,
      '#isFund' => $isFund,
      '#subject' => $subject
    ];
    $output = \Drupal::service('renderer')->renderPlain($template);


    $form['vapa_class'] = array(
      '#markup' => $output,
    );

    foreach ($results as $result){
      $form['vapa'] = array(
        '#type' => 'radio',
        '#required' => TRUE,
        '#title' => $this->t(''),
        '#default_value' => $this->store->get('vapa') ? $this->store->get('') : 0,
        '#return_value' => $result->field_course_number[0]->value,
      );
    }
 


    $form['actions']['submit']['#value'] = $this->t('Next');
    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('btn', 'btn-primary'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('multistep.multistep_pe_class_form')
     );


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('vapa', $form_state->getValue('vapa'));
    if($form_state->getTriggeringElement()['#id'] == 'edit-submit') {
      if ($form_state->getValue('vapa') != 0) {
      $form_state->setRedirect('multistep.multistep_vapa_class_form2');
      }
      else {
        $this->store->set('vapa2', $form_state->getValue('vapa'));
        $form_state->setRedirect('multistep.multistep_lang_class_form');
      }
    }
    else {
      $form_state->setRedirect('multistep.multistep_pe_class_form');
    }
  }
}

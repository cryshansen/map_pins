<?php


/**
 * @file
 * Contains \Drupal\Map Pins\Form\MapPinsForm.
 */
namespace Drupal\map_pins\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class MapPinsForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map_pins_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['full_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Full Name:'),
      '#required' => TRUE,
      '#attributes' => array(
        'placeholder' => t('Enter Full Name*...'),
      ),
      
    );

    $form['email'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
      '#attributes' => array(
        'placeholder' => t('Enter Email*...'),
      ),
    );    
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Join Group »'),
      '#button_type' => 'primary',
    );
    $form['#theme'] = 'map_pins_form';
    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if(strlen($form_state->getValue('full_name')) < 8) {
      $form_state->setErrorByName('full_name', $this->t('Please enter a valid full name'));
    }
    /*if(strlen($form_state->getValue('student_phone')) < 10) {
      $form_state->setErrorByName('student_phone', $this->t('Please enter a valid Contact Number'));
    }*/
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    /** TODO: call mailer */
    $this->sendmail( $form_state);

    \Drupal::messenger()->addMessage(t("Map Pins Done!! Map Pin Values are:"));

    try{
      $conn = Database::getConnection();
      
      $field = $form_state->getValues();
       
      $fields["first_name"] = $field['full_name'];
      $fields["last_name"] = $field['full_name'];
      $fields["email"] = $field['email'];
      $fields["long_latitude"] = '45.5019° N, 73.5674° W';
      
      
      $conn->insert('map_pins')->fields($fields)->execute();
      \Drupal::messenger()->addMessage($this->t('The Map Pin Location data has been succesfully saved'));
       
    } catch(Exception $ex){
      \Drupal::logger('map_pins')->error($ex->getMessage());
    }

  }
  
  public function sendmail(FormStateInterface $form_state){
    $message_body="";
    
    foreach ($form_state->getValues() as $key => $value) {
      $message_body .= ' ' . $key . ': ' . $value;
      }
    
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'map_pins';
      $key = 'general_mail';
      $to = "mail@mail.com"; /**$form_state->getValue('email') */
      $params['message'] = "This is the map pins message ".$message_body;
      $params['subject'] = "Mail subject -- Map Pins ";
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

   
      
      \Drupal::logger('my_module')->debug('<pre><code>' . print_r($params, TRUE) . '</code></pre>');
      // Logs an error  TODO: write try catch
      if ($result['result'] !== true) {
        \Drupal::logger('map_pins')->error(t('There was a problem sending your message and it was not sent.'));
        \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
      }
      else {
        \Drupal::messenger()->addMessage(t('Your message has been sent.'));

        /** \Drupal::logger('map_pins')->notice('@message: body sent to  %title.',
        [
            @type' => $this->entity->bundle(),
            '%title' => $this->entity->label(),
        ]); **/
        \Drupal::logger('map_pins')->notice(t('Your message has been sent.'));
      }

  }

}

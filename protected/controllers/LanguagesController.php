<?php

class LanguagesController extends Controller
{
    /**
     * Entry
     */
    public function actionIndex()
    {
        $this->actionList();
    }


    public function actionList($curr_lng = null,$search = null)
    {
        $request = Yii::app()->request;
        $lang_prefix = Yii::app()->language;
        $arrSelect = ExtLanguages::model()->selectArray();    
        
        if($request->isAjaxRequest){
            
            $select_lng = $request->getPost('lng');
            $search_label = $request->getPost('search_val');
            
            $arrLabel = ExtLanguages::model()->getLabels($select_lng,array('search_label' => $search_label));
            $retData = $this->renderPartial('_trl_list',array('arrLabel' => $arrLabel,
                    'arrSelect' => $arrSelect,'lang_prefix' => $lang_prefix,'select_lng' => $select_lng,
                    'search_val' => $search_label)); 
            echo $retData;
            exit();
        }else{
            
                  //if not admin - no access 
            if(Yii::app()->user->getState('role') != 3)
            {
                throw new CHttpException(404);
            }
            
            if(empty($curr_lng)){
               $curr_lng = $lang_prefix; 
            }
            $search_label = $request->getPost('search_label');  
            $arrLabel = ExtLanguages::model()->getLabels($curr_lng, array('search_label' => $search_label));
            $this->render('trl_list',array('arrLabel' => $arrLabel,
                    'arrSelect' => $arrSelect,'lang_prefix' => $lang_prefix,'select_lng' => $curr_lng,
                    'search_val' => $search_label)); 
            
        }
      
    }//actionList
    

    
      /**
     * Save translation
     * @param int $id label id
     * @throws CHttpException
     */
    public function actionSave($id = null){
        $request = Yii::app()->request;
        $curr_lng = $request->getPost('curr_lng');
        $value_label = trim($request->getPost('value'));
        $search_text = trim($request->getPost('search-text'));
        
        $objLabel = LabelsTrl::model()->findByPk((int)$id);
       
        $objLabel->value = $value_label;
        $objLabel->save();
         
        $this->redirect(array('list','curr_lng'=> $curr_lng, 'search' => $search_text)); 
    }//actionSave
    
    
    
    public function actionAddLabel(){
        
        $lang_prefix = Yii::app()->language;
        $request = Yii::app()->request;
        
        if($request->isAjaxRequest){
            $retData = $this->renderPartial('_add_label_modal',array('lang_prefix' =>$lang_prefix));
            echo $retData;
        }else{
            
            $label = $request->getPost('label_name');
            $arrLng = ExtLanguages::model()->getAllLang();
            ExtLabels::model()->addLabel($label,$arrLng);
            
            $this->redirect(array('list')); 
        }

    }
    
    
    /**
     * Delete Label
     * @param $id - int label ID
     */
    public function actionDelLabel($id = null){
        $lang_prefix = Yii::app()->language;
        $request = Yii::app()->request;
        
         if($request->isAjaxRequest){
            $id = $request->getPost('id');
            $name = $request->getPost('name');
            
            $retData = $this->renderPartial('_delete_label_modal',array('lang_prefix' =>$lang_prefix,
                        'id'=>$id,'label_name' => $name));
            echo $retData;
            exit();
         }else{
            $objLabel = Labels::model()->findByPk($id);
            $objLabel->delete();
            //ExtLabels::model()->deleteLabel($id);
            
            $this->redirect(array('list'));
         }
        
    }

    public function actionUniqueCeckLabel(){
        $label = $_POST['label'];
        if($user = Labels::model()->exists('label=:label',array('label'=>$label)))
        {
            $arrJson = array();
            $arrJson['status'] = "error";
            $arrJson['err_txt'] = Trl::t()->getMsg("Duplicate error");; 
            echo json_encode($arrJson);
        }
        else
        {
            $arrJson['status'] = "success";
            echo json_encode($arrJson);
        }


    }
    /* MESSAGES TRANSLATE */ 

  
    public function actionAddMes(){
        $lang_prefix = Yii::app()->language;
        $request = Yii::app()->request;
        
        if($request->isAjaxRequest){

            $retData = $this->renderPartial('_add_message_modal',array('lang_prefix' =>$lang_prefix));
            echo $retData;
            
        }else{
            
            $label = trim($request->getPost('label_name'));
            $arrLng = ExtLanguages::model()->getAllLang();
            ExtMessage::model()->addMessage($label,$arrLng);
            
            $this->redirect(array('listMes')); 
        }
    }
  
    public function actionDelMes($id = null){
        $lang_prefix = Yii::app()->language;
        $request = Yii::app()->request;
        
         if($request->isAjaxRequest){
            $id = $request->getPost('id');
            $name = $request->getPost('name');
            
            $retData = $this->renderPartial('_delete_message_modal',array('lang_prefix' =>$lang_prefix,
                        'id'=>$id,'label_name' => $name));
            echo $retData;
            exit();
         }else{
            $objMes = Message::model()->findByPk($id);

            $objMes->delete();

            //ExtLabels::model()->deleteLabel($id);
            
            $this->redirect(array('listMes'));
         }
        
    }

    public function actionSaveMes($id = null){
        $request = Yii::app()->request;
        $curr_lng = $request->getPost('curr_lng');
        $value_label = trim($request->getPost('translation'));
        $search_text = trim($request->getPost('search-text'));
        
        $objLabel = MessageTrl::model()->findByPk((int)$id);
       
        $objLabel->translation = $value_label;
        $objLabel->save();
         
        $this->redirect(array('listMes','curr_lng'=> $curr_lng, 'search' => $search_text)); 
    }//actionSave
    
    public function actionListMes($curr_lng = null,$search = null)
    {
        $request = Yii::app()->request;
        $lang_prefix = Yii::app()->language;
        $arrSelect = ExtLanguages::model()->selectArray();    
        
        if($request->isAjaxRequest){
            
            $select_lng = $request->getPost('lng');
            $search_label = $request->getPost('search_label');
            
            $arrLabel = ExtMessage::model()->getMessagesArr($select_lng,array('search_label' => $search_label));
            $retData = $this->renderPartial('_trlMes_list',array('arrLabel' => $arrLabel,
                    'arrSelect' => $arrSelect,'lang_prefix' => $lang_prefix,'select_lng' => $select_lng,
                    'search_val' => $search_label)); 
            echo $retData;
            exit();
        }else{
            
                  //if not admin - no access 
            if(Yii::app()->user->getState('role') != 3)
            {
                throw new CHttpException(404);
            }
            
            if(empty($curr_lng)){
               $curr_lng = $lang_prefix; 
            }

            $search_label = $request->getPost('search_label');

            $arrLabel = ExtMessage::model()->getMessagesArr($curr_lng, array('search_label' => $search_label));
            $this->render('trlMes_list',array('arrLabel' => $arrLabel,
                    'arrSelect' => $arrSelect,'lang_prefix' => $lang_prefix,'select_lng' => $curr_lng,
                    'search_val' => $search_label)); 
            
        }
      
    }//actionList

    public function actionMessages($curr_lng = null,$search = null)
    {
        $this->actionListMes();
    }

    public function actionUniqueCeckMessage(){
        $label = $_POST['label'];
        
        if($user = Message::model()->exists('text=:label',array('label'=>$label)))
        {
            $arrJson = array();
            $arrJson['status'] = "error";
            $arrJson['err_txt'] = Trl::t()->getMsg("Duplicate error");; 
            echo json_encode($arrJson);
        }
        else
        {
            $arrJson['status'] = "success";
            echo json_encode($arrJson);
        }
        
    }
    
    /* L A N G U A G E S */

    /**
     * Add language
     * @throws CHttpException
     */
    public function actionAddLanguage()
    {
        //If user not admin - no access for this page
        if(Yii::app()->user->getState('role') != 3)
        {
            throw new CHttpException(404);
        }

        //form validator
        $form = new LanguageForm();

        //if got POST from form
        if($_POST['LanguageForm'])
        {
            //assign attributes to form-validator
            $form->attributes = $_POST['LanguageForm'];

            //if form valid
            if($form->validate())
            {
                //create new language
                $language = new Languages();
                $language->attributes = $form->attributes;
                $language->save();

                //back to list
                $this->redirect(Yii::app()->createUrl('/languages/list'));
            }
        }

        //get statuses as array
        $statuses = Statusses::model()->getAllAsArray(0,2);

        //render form
        $this->render('language_add',array('form_mdl' => $form, 'statuses' => $statuses));
    }


    /**
     * Edit language
     * @param int $id
     * @throws CHttpException
     */
    public function actionEditLanguage($id = null)
    {
        //try find language
        $language = Languages::model()->findByPk($id);

        //If user not admin - no access for this page
        if(Yii::app()->user->getState('role') != 3 || empty($language))
        {
            throw new CHttpException(404);
        }

        //form validator
        $form = new LanguageForm();

        //if got POST from form
        if($_POST['LanguageForm'])
        {
            //assign attributes to form-validator
            $form->attributes = $_POST['LanguageForm'];

            //if form valid
            if($form->validate())
            {
                //create new language
                $language->attributes = $form->attributes;
                $language->update();

                //back to list
                $this->redirect(Yii::app()->createUrl('/languages/list'));
            }
        }

        //get statuses as array
        $statuses = Statusses::model()->getAllAsArray(0,2);

        //render form
        $this->render('language_edit',array('form_mdl' => $form, 'statuses' => $statuses, 'language' => $language));
    }


    /**
     * Delete language and translations
     * @param int $id
     * @throws CHttpException
     */
    public function actionDeleteLanguage($id = null)
    {
        /* @var $language Languages */

        //try find language
        $language = Languages::model()->findByPk($id);

        //If user not admin - no access for this page
        if(Yii::app()->user->getState('role') != 3 || empty($language))
        {
            throw new CHttpException(404);
        }

        //delete all translations by language id
        TranslationsLng::model()->deleteByLanguageId($language->id);

        //delete language
        $language->delete();

        //back to list
        $this->redirect(Yii::app()->createUrl('/languages/list'));
    }
    
    /* by Maxim */

    public function actionLangList()
    {
        $lang_prefix = Yii::app()->language;
        $langs = ExtLanguages::model()->getAllLang();
        $this->render('lang_list',array('langs' => $langs, 'lang_prefix' => $lang_prefix));
    }
  
  /* by Maxim End */

    /* T R A N S L A T I O N S */

    /**
     * Edit translation
     * @param int $id
     * @throws CHttpException
     */
    public function actionEditTrans($id = null)
    {
        //if not admin - no access
        if(Yii::app()->user->getState('role') != 3)
        {
            throw new CHttpException(404);
        }

        $translation = Translations::model()->findByPk($id);
        $languages = Languages::model()->findAll();

        $this->render('translation_edit',array('translation' => $translation, 'languages' => $languages));
    }

    /**
     * Add new translation
     * @throws CHttpException
     */
    public function actionAddTrans()
    {
        //if not admin - no access
        if(Yii::app()->user->getState('role') != 3)
        {
            throw new CHttpException(404);
        }

        $translation = new Translations();
        $languages = Languages::model()->findAll();

        $this->render('translation_edit',array('translation' => $translation, 'languages' => $languages));
    }


    /**
     * Update translation
     * @throws CHttpException
     */
    public function actionUpdateTrans()
    {
        //if not admin - no access
        if(Yii::app()->user->getState('role') != 3)
        {
            throw new CHttpException(404);
        }

        //get params from post
        $id = Yii::app()->request->getPost('id');
        $label = Yii::app()->request->getPost('label');
        $translations = Yii::app()->request->getPost('translation');

        //try find translation
        $translation = Translations::model()->findByPk($id);

        //if not found - create new one
        if(empty($translation))
        {
            $translation = new Translations();
        }

        //set label to translation
        $translation->label = $label;
        $translation->status = 1;

        //if created - save, if old - update
        if($translation->isNewRecord)
            $translation->save();
        else
            $translation->update();

        //for every translation value
        foreach($translations as $lng_id => $value)
        {
            //try find value by translation id and language
            $tr_lng = TranslationsLng::model()->findByAttributes(array('translation_id' => $translation->id, 'language_id' => $lng_id));

            //if not found - create new one
            if(empty($tr_lng))
            {
                $tr_lng = new TranslationsLng();
            }

            //if new - relate with translation and language, set value and save
            if($tr_lng->isNewRecord)
            {
                $tr_lng -> language_id = $lng_id;
                $tr_lng -> translation_id = $translation->id;
                $tr_lng -> value = $value;
                $tr_lng -> save();
            }
            //if old - just update value
            else
            {
                $tr_lng -> value = $value;
                $tr_lng -> update();
            }
        }

        //back to list
        $this->redirect(Yii::app()->createUrl('/languages/list'));
    }


    /**
     * Deletes translation
     * @param int $id
     * @throws CHttpException
     */
    public function actionDeleteTrans($id = null)
    {
        /* @var $translation Translations */

        //try find translation
        $translation = Translations::model()->findByPk($id);

        //If user not admin, or translation don't exist - no access for this page
        if(Yii::app()->user->getState('role') != 3 || empty($translation))
        {
            throw new CHttpException(404);
        }

        $translation->deleteTranslations(); //delete all translations
        $translation->delete(); //delete translation-unit

        //back to list
        $this->redirect(Yii::app()->createUrl('/languages/list'));
    }
}

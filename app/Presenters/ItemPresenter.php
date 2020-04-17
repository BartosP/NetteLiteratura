<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model;
use Nette\Application\UI;

final class ItemPresenter extends BasePresenter {
	private $itemManager;

	public function __construct(Model\ItemManager $itemManager) {
		$this->itemManager = $itemManager;
	}

	public function renderList($order = 'title ASC'): void {
		$this->template->itemList = $this->itemManager->getAll($order);
	}

	public function renderDetail($id): void {
		$this->template->item = $this->itemManager->getById($id);
	}

	public function actionInsert(): void {
		$this['itemForm']['stars']->setDefaultValue('3');
		$this['itemForm']['category']->setDefaultValue('drama');   
	}
   
	public function actionUpdate($id): void {
		$data = $this->itemManager->getById($id)->toArray();
		$this['itemForm']->setDefaults($data);
	}
   
	public function actionDelete($id): void {
		if ($this->itemManager->delete($id)) {
			$this->flashMessage('Záznam byl úspěšně smazán', 'success');
		}
		else{
			$this->flashMessage('Došlo k nějaké chybě při mazání záznamu', 'danger');
		}	   
		$this->redirect('list');
	}

	protected function createComponentItemForm(): UI\Form {
		$form = new UI\Form;
		$form->addText('title', 'Název díla:')
			->addRule(UI\Form::MIN_LENGTH, 'Musí obsahovat aspoň 5 znaků', '5')
 			->setRequired(true);
		$form->addText('author', 'Autor:')
			->addRule(UI\Form::MIN_LENGTH, 'Musí obsahovat aspoň 5 znaků', '5')
			->setRequired(true);
		$form->addTextArea('anotation', 'Stručná charakteristika díla:')
 			->setHtmlAttribute('rows', '6')
			->setRequired(true);
		$form->addInteger('year', 'Rok vzniku:')
			->setRequired(true);
		$kategorie = [
			'drama' => 'drama',
			'poezie' => 'poezie',
			'próza' => 'próza'
		];
		$form->addRadioList('category', 'Kategorie:', $kategorie)
			->setRequired(true);
		$hodnoceni = [
			'1' => '*',
			'2' => '**',
			'3' => '***',
			'4' => '****',
			'5' => '*****'
		];
		$form->addSelect('stars', 'Hodnocení:', $hodnoceni)
			->setRequired(true);
		$form->addSubmit('submit', 'Potvrdit');
		$form->onSuccess[] = [$this, 'itemFormSucceeded'];
		return $form;
	}

	public function itemFormSucceeded(UI\Form $form, $values): void {
		$itemId = $this->getParameter('id');
		if ($itemId) {
			$item = $this->itemManager->update($itemId, $values);
		}
		else {
			$item = $this->itemManager->insert($values);
		}
		if ($item) {
			$this->flashMessage('Akce byla úspěšně uložena', 'success');
		}
		else {
			$this->flashMessage('Došlo k nějaké chybě při ukládání do databáze', 'danger');
		}
		$this->redirect('Item:list');
	}
}

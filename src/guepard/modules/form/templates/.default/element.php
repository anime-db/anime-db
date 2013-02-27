  <tr<? if($this->getViewParams('class')):?> class="form-<?=$this->getViewParams('class')?>-field"<? endif?>>
    <th class="form-field-title"><?=$this->getTitle()?><? if($this->isRequired()):?><span class="form-field-required">*</span><? endif?>:<? if($this->getComment()):?><div class="form-field-comment">(<?=$this->getComment()?>)</div><? endif?></th>
    <td class="form-field-input"><? $this->drawField()?></td>
  </tr>

var alreadySetSkuVals={};$(function(){$(document).on("change",'.sku_value',function(){getAlreadySetSkuVals();console.log(alreadySetSkuVals);var b=true;var skuTypeArr=[];var totalRow=1;$(".SKU_TYPE").each(function(){var skuTypeNode=$(this).children("li");var skuTypeObj={};skuTypeObj.skuTypeTitle=$(skuTypeNode).attr("sku-type-name");var propid=$(skuTypeNode).attr("propid");skuTypeObj.skuTypeKey=propid;var is_required=$(skuTypeNode).attr("is_required");skuValueArr=[];var skuValNode=$(this).next();var skuValCheckBoxs=$(skuValNode).find("input[type='checkbox'][class*='sku_value']");var checkedNodeLen=0;$(skuValCheckBoxs).each(function(){if($(this).is(":checked")){var skuValObj={};skuValObj.skuValueTitle=$(this).val();skuValObj.skuValueId=$(this).attr("propvalid");skuValObj.skuPropId=$(this).attr("propid");skuValueArr.push(skuValObj);checkedNodeLen++;}});if(is_required&&"1"==is_required){if(checkedNodeLen<=0){b=false;return false;}}
if(skuValueArr&&skuValueArr.length>0){totalRow=totalRow*skuValueArr.length;skuTypeObj.skuValues=skuValueArr;skuTypeObj.skuValueLen=skuValueArr.length;skuTypeArr.push(skuTypeObj);}});var SKUTableDom="";if(b){SKUTableDom+="<table class='skuTable'><tr>";for(var t=0;t<skuTypeArr.length;t++){SKUTableDom+='<th>'+skuTypeArr[t].skuTypeTitle+'</th>';}
SKUTableDom+='<th>价格</th><th>库存</th>';SKUTableDom+="</tr>";for(var i=0;i<totalRow;i++){var currRowDoms="";var rowCount=1;var propvalidArr=[];var propIdArr=[];var propvalnameArr=[];var propNameArr=[];for(var j=0;j<skuTypeArr.length;j++){var skuValues=skuTypeArr[j].skuValues;var skuValueLen=skuValues.length;rowCount=(rowCount*skuValueLen);var anInterBankNum=(totalRow/rowCount);var point=((i/anInterBankNum)%skuValueLen);propNameArr.push(skuTypeArr[j].skuTypeTitle);if(0==(i%anInterBankNum)){currRowDoms+='<td rowspan='+anInterBankNum+'>'+skuValues[point].skuValueTitle+'</td>';propvalidArr.push(skuValues[point].skuValueId);propIdArr.push(skuValues[point].skuPropId);propvalnameArr.push(skuValues[point].skuValueTitle);}else{propvalidArr.push(skuValues[parseInt(point)].skuValueId);propIdArr.push(skuValues[parseInt(point)].skuPropId);propvalnameArr.push(skuValues[parseInt(point)].skuValueTitle);}}
var propvalids=propvalidArr.toString()
var alreadySetSkuPrice="";var alreadySetSkuStock="";if(alreadySetSkuVals){var currGroupSkuVal=alreadySetSkuVals[propvalids];if(currGroupSkuVal){alreadySetSkuPrice=currGroupSkuVal.skuPrice;alreadySetSkuStock=currGroupSkuVal.skuStock}}
SKUTableDom+='<tr propvalids=\''+propvalids+'\' propids=\''+propIdArr.toString()+'\' propvalnames=\''+propvalnameArr.join(";")+'\'  propnames=\''+propNameArr.join(";")+'\' class="sku_table_tr">'+currRowDoms+'<td><input type="text" class="setting_sku_price" value="'+alreadySetSkuPrice+'"/></td><td><input type="text" class="setting_sku_stock" value="'+alreadySetSkuStock+'"/></td></tr>';}
SKUTableDom+="</table>";}
$("#skuTable").html(SKUTableDom);});});function getAlreadySetSkuVals(){alreadySetSkuVals={};$("tr[class*='sku_table_tr']").each(function(){var skuPrice=$(this).find("input[type='text'][class*='setting_sku_price']").val();var skuStock=$(this).find("input[type='text'][class*='setting_sku_stock']").val();if(skuPrice||skuStock){var propvalids=$(this).attr("propvalids");alreadySetSkuVals[propvalids]={"skuPrice":skuPrice,"skuStock":skuStock}}});}
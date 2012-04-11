var msgCt;

function createBox(t, s){ldelim}
    return ['<div class="msg">',
            '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
            '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
            '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
            '</div>'].join('');
{rdelim}

function show_feedback(title, format){ldelim}
    if(!msgCt)
    {ldelim}
        msgCt = Ext.DomHelper.insertFirst(document.body, {ldelim}id:'feedback-div'{rdelim}, true);
    {rdelim}
    msgCt.alignTo(document, 't-t');
    var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.DomHelper.append(msgCt, {ldelim}html:createBox(title, s){rdelim}, true);
    m.slideIn('t').pause(1).ghost("t", {ldelim}remove:true{rdelim});
{rdelim}

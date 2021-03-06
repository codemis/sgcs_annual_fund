/***
 * Twitter JS v1.13.3
 * http://code.google.com/p/twitterjs/
 * Copyright (c) 2009 Remy Sharp / MIT License
 * $Date: 2011-07-04 15:40:40 +0100 (Mon, 04 Jul 2011) $
 */
 /*
  MIT (MIT-LICENSE.txt)
 */
typeof getTwitters!="function"&&function(){var a={},b=0;!function(a,b){function m(a){l=1;while(a=c.shift())a()}var c=[],d,e,f=!1,g=b.documentElement,h=g.doScroll,i="DOMContentLoaded",j="addEventListener",k="onreadystatechange",l=/^loade|c/.test(b.readyState);b[j]&&b[j](i,e=function(){b.removeEventListener(i,e,f),m()},f),h&&b.attachEvent(k,d=function(){/^c/.test(b.readyState)&&(b.detachEvent(k,d),m())}),a.domReady=h?function(a){self!=top?l?a():c.push(a):function(){try{g.doScroll("left")}catch(b){return setTimeout(function(){domReady(a)},50)}a()}()}:function(a){l?a():c.push(a)}}(a,document),window.getTwitters=function(c,d,e,f){b++,typeof d=="object"&&(f=d,d=f.id,e=f.count),e||(e=1),f?f.count=e:f={},!f.timeout&&typeof f.onTimeout=="function"&&(f.timeout=10),typeof f.clearContents=="undefined"&&(f.clearContents=!0),f.twitterTarget=c,typeof f.enableLinks=="undefined"&&(f.enableLinks=!0),a.domReady(function(a,b){return function(){function f(){a.target=document.getElementById(a.twitterTarget);if(!!a.target){var f={limit:e};f.includeRT&&(f.include_rts=!0),a.timeout&&(window["twitterTimeout"+b]=setTimeout(function(){twitterlib.cancel(),a.onTimeout.call(a.target)},a.timeout*1e3));var g="timeline";d.indexOf("#")===0&&(g="search"),d.indexOf("/")!==-1&&(g="list"),a.ignoreReplies&&(f.filter="-@"),twitterlib.cache(!0),twitterlib[g](d,f,function(b,d){var e=[],f=b.length>a.count?a.count:b.length;e=["<ul>"];for(var g=0;g<f;g++){b[g].time=twitterlib.time.relative(b[g].created_at);for(var h in b[g].user)b[g]["user_"+h]=b[g].user[h];a.template?e.push("<li>"+a.template.replace(/%([a-z_\-\.]*)%/ig,function(c,d){var e=b[g][d]+""||"";d=="text"&&(e=twitterlib.expandLinks(b[g])),d=="text"&&a.enableLinks&&(e=twitterlib.ify.clean(e));return e})+"</li>"):a.bigTemplate?e.push(twitterlib.render(b[g])):e.push(c(b[g]))}e.push("</ul>"),a.clearContents?a.target.innerHTML=e.join(""):a.target.innerHTML+=e.join(""),a.callback&&a.callback(b)})}}function c(b){var c=a.enableLinks?twitterlib.ify.clean(twitterlib.expandLinks(b)):twitterlib.expandLinks(b),d="<li>";a.prefix&&(d+='<li><span className="twitterPrefix">',d+=a.prefix.replace(/%(.*?)%/g,function(a,c){return b.user[c]}),d+=" </span></li>"),d+='<span className="twitterStatus">'+twitterlib.time.relative(b.created_at)+"</span> ",d+='<span className="twitterTime">'+b.text+"</span>",a.newwindow&&(d=d.replace(/<a href/gi,'<a target="_blank" href'));return d}typeof twitterlib!="function"?setTimeout(function(){var a=document.createElement("script");a.onload=a.onreadystatechange=f,a.src="https://github.com/remy/twitterlib/raw/master/twitterlib.min.js";var b=document.head||document.getElementsByTagName("head")[0];b.insertBefore(a,b.firstChild)},0):f()}}(f,b))}}()

//twitter id and tweet count can be changed here
<!--
	getTwitters('twitter', {
        id: 'envato', 
        count: 1, 
        enableLinks: true, 
        template: '<a href="#" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color:#d98f38; padding-right: 5px;">@younic</a><br/><span class="twitterPrefix"><span class="twitterStatus">%text%</span> <span class="username"><a href="http://twitter.com/%user_screen_name%"><br/></span>',
        newwindow: true
});
-->

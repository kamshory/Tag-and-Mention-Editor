/**
 * jQuery plugin for getting position of cursor in textarea

 * @license under Apache license
 * @author Bevis Zhao (i@bevis.me, http://bevis.me)
 */

(function($, window, document, undefined) {
	$(function() {
		var calculator = {
			// key styles
			primaryStyles: ['fontFamily', 'fontSize', 'fontWeight', 'fontVariant', 'fontStyle',
				'paddingLeft', 'paddingTop', 'paddingBottom', 'paddingRight',
				'marginLeft', 'marginTop', 'marginBottom', 'marginRight',
				'borderLeftColor', 'borderTopColor', 'borderBottomColor', 'borderRightColor',
				'borderLeftStyle', 'borderTopStyle', 'borderBottomStyle', 'borderRightStyle',
				'borderLeftWidth', 'borderTopWidth', 'borderBottomWidth', 'borderRightWidth',
				'line-height', 'outline'],

			specificStyle: {
				'word-wrap': 'break-word',
				'overflow-x': 'hidden',
				'overflow-y': 'auto'
			},

			simulator : $('<div id="textarea_simulator" contenteditable="true"/>').css({
				position: 'absolute',
				top: 0,
				left: 0,
				visibility: 'hidden'
			}).appendTo(document.body),

			toHtml : function(text) {
				return text.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g, '<br>')
					.replace(/(\s)/g,'<span style="white-space:pre-wrap;">$1</span>');
			},
			// calculate position
			getCaretPosition: function() {
				var cal = calculator, self = this, element = self[0], elementOffset = self.offset();

				// IE has easy way to get caret offset position
				if ($.browser.msie) {
					// must get focus first
					element.focus();
					var range = document.selection.createRange();
					return {
						left: range.boundingLeft - elementOffset.left,
						top: parseInt(range.boundingTop) - elementOffset.top + element.scrollTop
							+ document.documentElement.scrollTop + parseInt(self.getComputedStyle("fontSize"))
					};
				}
				cal.simulator.empty();
				// clone primary styles to imitate textarea
				$.each(cal.primaryStyles, function(index, styleName) {
					self.cloneStyle(cal.simulator, styleName);
				});

				// caculate width and height
				cal.simulator.css($.extend({
					'width': self.width(),
					'height': self.height()
				}, cal.specificStyle));

				var value = self.val(), cursorPosition = self.getCursorPosition();
				var beforeText = value.substring(0, cursorPosition),
					afterText = value.substring(cursorPosition);

				var before = $('<span class="before"/>').html(cal.toHtml(beforeText)),
					focus = $('<span class="focus"/>'),
					after = $('<span class="after"/>').html(cal.toHtml(afterText));

				cal.simulator.append(before).append(focus).append(after);
				var focusOffset = focus.offset(), simulatorOffset = cal.simulator.offset();
				
				
				
				
				
				// alert(focusOffset.left  + ',' +  simulatorOffset.left + ',' + element.scrollLeft);
				return {
					top: focusOffset.top - simulatorOffset.top - element.scrollTop
						// calculate and add the font height except Firefox
						+ ($.browser.mozilla ? 0 : parseInt(self.getComputedStyle("fontSize"))),
					left: focus[0].offsetLeft -  cal.simulator[0].offsetLeft - element.scrollLeft
				};
			}
		};

		$.fn.extend({
			getComputedStyle: function(styleName) {
				if (this.length == 0) return;
				var thiz = this[0];
				var result = this.css(styleName);
				result = result || ($.browser.msie ?
					thiz.currentStyle[styleName]:
					document.defaultView.getComputedStyle(thiz, null)[styleName]);
				return result;
			},
			// easy clone method
			cloneStyle: function(target, styleName) {
				var styleVal = this.getComputedStyle(styleName);
				if (!!styleVal) {
					$(target).css(styleName, styleVal);
				}
			},
			cloneAllStyle: function(target, style) {
				var thiz = this[0];
				for (var styleName in thiz.style) {
					var val = thiz.style[styleName];
					typeof val == 'string' || typeof val == 'number'
						? this.cloneStyle(target, styleName)
						: NaN;
				}
			},
			getControlStatus : function() {
				var thiz = this[0], result = 0;
				var textCursorPos = 0;
				var text = thiz.value;
				var whiteSpace = " \r\n\t";
				if ('selectionStart' in thiz) 
				{
					result = thiz.selectionStart;
					textCursorPos = result;
				} 
				else if('selection' in document) 
				{
					var range = document.selection.createRange();
					if (parseInt($.browser.version) > 6) 
					{
						thiz.focus();
						var length = document.selection.createRange().text.length;
						range.moveStart('character', - thiz.value.length);
						result = range.text.length - length;
						textCursorPos = result;
					} 
					else 
					{
						var bodyRange = document.body.createTextRange();
						bodyRange.moveToElementText(thiz);
						for (; bodyRange.compareEndPoints("StartToStart", range) < 0; result++)
							bodyRange.moveStart('character', 1);
						for (var i = 0; i <= result; i ++){
							if (thiz.value.charAt(i) == '\n')
								result++;
						}
						var enterCount = thiz.value.split('\n').length - 1;
						result -= enterCount;
						textCursorPos = result;
					}
				}
				else
				{
					textCursorPos = result;
				}
				
				var offsetText = textCursorPos;
				var mustBlank = false;
				var selectedWord = '';
				var i, j, k, l, start = 0, end = 0, length = text.length;
				if(offsetText > 0)
				{
					if(whiteSpace.indexOf(text.substr(offsetText, 1)) > -1 &&  whiteSpace.indexOf(text.substr(offsetText-1, 1)) == -1)
					{
						offsetText--;
					}
				}
				else if(offsetText < text.length-1)
				{
					if(whiteSpace.indexOf(text.substr(offsetText, 1)) > -1 &&  whiteSpace.indexOf(text.substr(offsetText+1, 1)) == -1)
					{
						offsetText++;
					}
				}
				else if(offsetText < text.length-1)
				{
					if(whiteSpace.indexOf(text.substr(offsetText, 1)) > -1 && (whiteSpace.indexOf(text.substr(offsetText+1, 1)) > -1 || whiteSpace.indexOf(text.substr(offsetText+1, 1)) > -1))
					{
						mustBlank = true;
						start = offsetText;
						end = offsetText;
					}
				}
				
				if(!mustBlank)
				{
					for(i = offsetText; i >= 0; i--)
					{
						if(whiteSpace.indexOf(text.substr(i, 1)) > -1)
						{
							break;
						}
						
					}
					start = i+1;
					for(i = offsetText; i < length; i++)
					{
						end = i;
						if(whiteSpace.indexOf(text.substr(i, 1)) > -1)
						{
							break;
						}
					}
					end = i;
					if(start < 0) start = 0;
					selectedWord = text.substr(start, end-start);
					selectedWord = selectedWord.trim('\t').trim('\r').trim('\n');
				}
				return {
					cursorPosition: textCursorPos, 
					selectedWord:selectedWord, 
					start:start, 
					end:end
					};
			},
			getCursorPosition : function() {
				var thiz = this[0], result = 0;
				if ('selectionStart' in thiz) {
					result = thiz.selectionStart;
				} else if('selection' in document) {
					var range = document.selection.createRange();
					if (parseInt($.browser.version) > 6) {
						thiz.focus();
						var length = document.selection.createRange().text.length;
						range.moveStart('character', - thiz.value.length);
						result = range.text.length - length;
					} else {
						var bodyRange = document.body.createTextRange();
						bodyRange.moveToElementText(thiz);
						for (; bodyRange.compareEndPoints("StartToStart", range) < 0; result++)
							bodyRange.moveStart('character', 1);
						for (var i = 0; i <= result; i ++){
							if (thiz.value.charAt(i) == '\n')
								result++;
						}
						var enterCount = thiz.value.split('\n').length - 1;
						result -= enterCount;
						return result;
					}
				}
				return result;
			},
			getCaretPosition: calculator.getCaretPosition
		});
	});
})(jQuery, window, document);

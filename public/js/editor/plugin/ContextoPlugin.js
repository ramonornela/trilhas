/**
 * @requires Xquared.js
 * @requires Browser.js
 * @requires Editor.js
 * @requires plugin/Base.js
 * @requires ui/Control.js
 */
xq.plugin.ContextoPlugin = xq.Class(xq.plugin.Base,
	/**
	 * @name xq.plugin.CustomStylePlugin
	 * @lends xq.plugin.CustomStylePlugin.prototype
	 * @extends xq.plugin.Base
	 * @constructor
	 */
	{
	onAfterLoad: function(xed) {
		xed.config.defaultToolbarButtonGroups.color.push(
			{className:"contexto", title:xed._("Contexto"), list: [
				{html:"<span style='background: yellow'> Pontuação </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('yellow')"},
				{html:"<span style='background: orange'> Ortografia </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('orange')"},
				{html:"<span style='background: red'> Acentuação </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('red')"},
				{html:"<span style='background: pink'> Crase </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('pink')"},
				{html:"<span style='background: #9FA0FF'> Regência </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#9FA0FF')"},
				{html:"<span style='background: #5F7F5F'> Concordância </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#5F7F5F')"},
				{html:"<span style='background: #00FFFF'> Coesão e coerência </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#00FFFF')"},
				{html:"<span style='background: #6CBF6B'> Rima/cacofonia </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#6CBF6B')"},
				{html:"<span style='background: #00FF00'> Clichê/repetição </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#00FF00')"},
				{html:"<span style='background: #AAA'> Outros </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#AAA')"},
				{html:"<span style='background: #D500FF'> Acordo ortográfico </span>", style: {marginBottom: "3px"}, handler:"xed.handleBackgroundColor('#D500FF')"}
			]}
		)
	}
});
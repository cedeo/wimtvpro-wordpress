/**
 * require('programming.js')
 */

ProgUtils.api = {};

ProgUtils.api.getBaseUrl = function (){ 
	return url_pathPlugin;
};


/*** GET ***/

/**
 * API relativa agli item di un palinsesto in un dato periodo di tempo
 * 
 * @param: progId	il programming identifier di riferimento
 */
ProgUtils.api.calendar = function(progId) {
	return  url_pathPlugin + 'rest/calendar.php?progId='+progId;
};

/**
 * Torna HTML del pool di video da usare come base 
 * per i vari “giorni” del calendario in cui si crea una programmazione
 */
ProgUtils.api.pool = function() {
	return  url_pathPlugin + 'rest/pool.php';
};

/**
 * Torna HTML del pool di video da usare come base 
 * per i vari “giorni” del calendario in cui si crea una programmazione
 */
ProgUtils.api.itemsAt = function() {
	return  url_pathPlugin + 'rest/currentProgramming.php';
};

/*** POST ***/

/**
 * Aggiunge/modifica informazioni generali palinsesto (es.nome)
 */
ProgUtils.api.programmingInfo = function() {
	return url_pathPlugin + 'rest/programmings.php';
};

/**
 * Aggiunge Item al palinsesto in un dato momento
 * 
 * @param: progId	il programming identifier di riferimento
 */
ProgUtils.api.addItem = function(progId) {
	return url_pathPlugin + 'rest/addItem.php?progId=' + progId;
};


/*** DELETE ***/

/**
 * rimuove eventi dal palinsesto, 
 * corrispondente ad un giorno solare nel calendario
 * 
 * @param: progId	il programming identifier di riferimento
 * 
 * JQUERY BUG in DELETE
 * non appende i parametri in data su query string
 */
ProgUtils.api.deleteItems = ProgUtils.api.removeItem;

/**
 * Elimina Item dal palinsesto
 * 
 * @param: progId	il programming identifier di riferimento
 * @param: itemId	ref. a item da eliminare
 */
ProgUtils.api.removeItem = function(progId, itemId) {
	return url_pathPlugin + 'rest/deleteItems.php?progId=' + progId + '&item='+ itemId;
};

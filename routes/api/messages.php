<?php



Route::get('/threads/mine', 'MessagesController@threads');
Route::get('/thread/{threadid}', 'MessagesController@getThread');
Route::get('/thread/markread/{threadid}', 'MessagesController@markThreadRead');
Route::get('/thread/message/markread/{threadid}', 'MessagesController@markMessageRead');
// Route::get('/thread/{threadid}/messages', 'MessagesController@getThreadMessages');

Route::post('/thread', 'MessagesController@newThread');
Route::post('/thread/{threadid}/reply', 'MessagesController@replyToThread');

Route::post('/message/remove/{id}', 'MessagesController@removeMessage');
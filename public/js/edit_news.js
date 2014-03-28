function addNews() {
  $("#news_form [name='news_id']").val("");
  $("#news_form [name='news_title']").val("");
  $("#news_form [name='news_body']").val("");
  $("#news_form [name='section']").val(currentSection);
  $("#news_form #delete_link").hide();
  $("#news_form").slideDown();
}

function dismissNewsForm() {
  $("#news_form").slideUp();
}

function editNews(newsId) {
  $("#news_form [name='news_id']").val(newsId);
  $("#news_form [name='news_title']").val(news[newsId].title);
  $("#news_form [name='news_body']").val(news[newsId].body);
  $("#news_form [name='section']").val(news[newsId].section);
  $("#news_form #delete_link").attr('href', news[newsId].delete_url);
  $("#news_form #delete_link").show();
  $("#news_form").slideDown();
}

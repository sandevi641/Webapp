// public/assets/app.js
document.addEventListener('DOMContentLoaded', function(){
  var ta = document.getElementById('content');
  var preview = document.getElementById('preview');
  if (!ta || !preview) return;

  // very small client-side markdown preview (matches server rules lightly)
  function mdToHtml(text) {
    // escape
    text = text.replace(/&/g, '&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    // code blocks
    text = text.replace(/```([\s\S]*?)```/g, function(m, p){ return '<pre><code>' + p + '</code></pre>'; });
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    // headings
    text = text.replace(/^######\s*(.+)$/gm,'<h6>$1</h6>');
    text = text.replace(/^#####\s*(.+)$/gm,'<h5>$1</h5>');
    text = text.replace(/^####\s*(.+)$/gm,'<h4>$1</h4>');
    text = text.replace(/^###\s*(.+)$/gm,'<h3>$1</h3>');
    text = text.replace(/^##\s*(.+)$/gm,'<h2>$1</h2>');
    text = text.replace(/^#\s*(.+)$/gm,'<h1>$1</h1>');
    // bold/italic - naive
    text = text.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>');
    text = text.replace(/\*(.+?)\*/g,'<em>$1</em>');
    // links
    text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>');
    // paragraphs
    var paras = text.split(/\n{2,}/);
    return paras.map(p => '<p>' + p.replace(/\n/g,'<br>') + '</p>').join('\n');
  }

  function update() {
    preview.innerHTML = mdToHtml(ta.value || '');
  }
  ta.addEventListener('input', update);
  update();
});

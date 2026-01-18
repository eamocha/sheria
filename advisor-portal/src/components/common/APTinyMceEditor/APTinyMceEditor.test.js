import React from 'react';
import ReactDOM from 'react-dom';
import APTinyMceEditor from './APTinyMceEditor';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APTinyMceEditor />, div);
  ReactDOM.unmountComponentAtNode(div);
});
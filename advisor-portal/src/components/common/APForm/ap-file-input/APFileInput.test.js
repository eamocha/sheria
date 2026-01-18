import React from 'react';
import ReactDOM from 'react-dom';
import APFileInput from './APFileInput';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APFileInput />, div);
  ReactDOM.unmountComponentAtNode(div);
});
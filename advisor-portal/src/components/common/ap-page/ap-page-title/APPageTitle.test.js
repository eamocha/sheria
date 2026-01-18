import React from 'react';
import ReactDOM from 'react-dom';
import APPageTitle from './APPageTitle';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPageTitle />, div);
  ReactDOM.unmountComponentAtNode(div);
});
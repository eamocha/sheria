import React from 'react';
import ReactDOM from 'react-dom';
import APCollapseRow from './APCollapseRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APCollapseRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
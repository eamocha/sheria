import React from 'react';
import ReactDOM from 'react-dom';
import APCollapseRowItem from './APCollapseRowItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APCollapseRowItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});
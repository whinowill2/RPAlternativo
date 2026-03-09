import classNames from 'classnames';
import { Link } from 'react-router-dom';
import '../styles/button.css';

const Button = ({ variant = 'light', icon: Icon, children, className, onClick, style, to, href, ...props }) => {
    const combinedClassName = classNames('ui-btn', `ui-btn-${variant}`, className);

    const innerContent = (
        <>
            {children && <span className="ui-btn-text">{children}</span>}
            {Icon && <Icon size={20} className="ui-btn-icon" />}
        </>
    );

    if (to) {
        return (
            <Link to={to} className={combinedClassName} style={style} {...props}>
                {innerContent}
            </Link>
        );
    }

    if (href) {
        return (
            <a href={href} className={combinedClassName} style={style} target="_blank" rel="noopener noreferrer" {...props}>
                {innerContent}
            </a>
        );
    }

    return (
        <button
            className={combinedClassName}
            onClick={onClick}
            style={style}
            {...props}
        >
            {innerContent}
        </button>
    );
};

export default Button;
